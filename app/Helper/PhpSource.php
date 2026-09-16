<?php

namespace Phuture\App\Helper;

use Throwable;

class PhpSource
{
    /**
     * Keywords a type is declared with
     */
    protected const DECLARATIONS = [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM];

    /**
     * Keywords standing between a docblock and the member it belongs to
     */
    protected const MODIFIERS = [T_ABSTRACT, T_FINAL, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_VAR, T_READONLY];

    /**
     * Tokens carrying nothing a reader of the source needs
     */
    protected const TRIVIA = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];

    /**
     * Types a source declares, with the members and docblocks of each
     *
     * The source is read rather than run. It is a file from somebody else's
     * repository, whose own dependencies are nowhere to be loaded from, so
     * including it would either run what it holds or stop the run outright.
     *
     * @param string $code Source to read
     * @return array|null Namespace and types, or null when the source is not valid php
     */
    public static function types(string $code): ?array
    {
        $tokens = self::tokens(self::encoding($code));

        if ($tokens === null) {
            return null;
        }

        $count = count($tokens);
        $namespace = '';
        $types = [];
        $type = null;
        $doc = null;
        $depth = 0;

        for ($index = 0; $index < $count; $index++) {
            $id = $tokens[$index]['id'];
            $text = $tokens[$index]['text'];

            if (in_array($id, [T_WHITESPACE, T_COMMENT, T_OPEN_TAG, T_INLINE_HTML], true)) {
                continue;
            }

            // Held for whatever is declared next, as an attribute or a modifier may stand in between
            if ($id === T_DOC_COMMENT) {
                $doc = $text;

                continue;
            }

            if ($id === T_ATTRIBUTE) {
                $index = self::attribute($tokens, $index);

                continue;
            }

            if (in_array($id, self::MODIFIERS, true)) {
                continue;
            }

            if ($id === T_NAMESPACE && $depth === 0) {
                $next = self::significant($tokens, $index + 1);
                $namespace = in_array($tokens[$next]['id'], [T_STRING, T_NAME_QUALIFIED], true) ? $tokens[$next]['text'] : '';
                $doc = null;

                continue;
            }

            if ($depth === 0 && self::declares($tokens, $index)) {
                $name = self::significant($tokens, $index + 1);

                $types[] = [
                    'kind' => mb_strtolower($text),
                    'name' => $tokens[$name]['text'],
                    'header' => self::header($tokens, $index),
                    'doc' => PhpDoc::parse($doc),
                    'members' => [],
                ];

                $type = count($types) - 1;
                $doc = null;

                continue;
            }

            // A body opens here, be it of a type, of a method, or of a string holding a variable
            if ($text === '{' || $id === T_CURLY_OPEN || $id === T_DOLLAR_OPEN_CURLY_BRACES) {
                $depth++;
                $doc = null;

                continue;
            }

            if ($text === '}') {
                $depth--;
                $doc = null;

                continue;
            }

            // Only what a type declares itself, rather than whatever the bodies below it hold
            if ($depth === 1 && $type !== null) {
                if ($id === T_FUNCTION) {
                    $name = self::significant($tokens, $index + 1);

                    // A method handing back a reference wears an ampersand where its name belongs
                    if ($tokens[$name]['text'] === '&') {
                        $name = self::significant($tokens, $name + 1);
                    }

                    $types[$type]['members'][] = [
                        'kind' => 'method',
                        'name' => $tokens[$name]['text'],
                        'signature' => self::signature($tokens, $index),
                        'doc' => PhpDoc::parse($doc),
                    ];

                    // Past the signature, so that a promoted property is never read as a member of its own
                    $index = self::closing($tokens, $index) - 1;
                    $doc = null;

                    continue;
                }

                if ($id === T_CONST || $id === T_CASE) {
                    $end = self::statement($tokens, $index);
                    $signature = self::spacing(self::between($tokens, $index, $end));

                    foreach (self::names($tokens, $index, $end) as $name) {
                        $types[$type]['members'][] = [
                            'kind' => $id === T_CONST ? 'constant' : 'case',
                            'name' => $name,
                            'signature' => $signature,
                            'doc' => PhpDoc::parse($doc),
                        ];
                    }

                    $index = $end;
                    $doc = null;

                    continue;
                }
            }

            $doc = null;
        }

        return ['namespace' => $namespace, 'types' => $types];
    }

    /**
     * Source read as UTF-8, whichever way it was written
     *
     * @param string $code Source to read
     * @return string
     */
    public static function encoding(string $code): string
    {
        // A byte order mark belongs to the file rather than to the source it holds
        $code = (string) preg_replace('/^\x{FEFF}/u', '', $code);

        if (mb_check_encoding($code, 'UTF-8')) {
            return $code;
        }

        return (string) mb_convert_encoding($code, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
    }

    /**
     * Tokens of a source, or null when it cannot be read as php
     *
     * The parser is asked for the tokens rather than the lexer, so that a name
     * reading like a keyword stays a name and a source that does not parse says so.
     *
     * @param string $code Source to tokenize
     * @return array|null
     */
    protected static function tokens(string $code): ?array
    {
        try {
            $raw = token_get_all($code, TOKEN_PARSE);
        } catch (Throwable) {
            return null;
        }

        $tokens = [];

        // A token of one character carries no id of its own, and zero is an id no token has
        foreach ($raw as $token) {
            $tokens[] = is_array($token) ? ['id' => $token[0], 'text' => $token[1]] : ['id' => 0, 'text' => $token];
        }

        return $tokens;
    }

    /**
     * Whether a keyword opens a declaration, rather than naming something
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index of the keyword
     * @return bool
     */
    protected static function declares(array $tokens, int $index): bool
    {
        if (!in_array($tokens[$index]['id'], self::DECLARATIONS, true)) {
            return false;
        }

        $previous = self::significant($tokens, $index - 1, -1);
        $behind = [T_NEW, T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR];

        // A class behind new has no name to be written under, and one behind an arrow is a member being named
        if ($previous >= 0 && in_array($tokens[$previous]['id'], $behind, true)) {
            return false;
        }

        return $tokens[self::significant($tokens, $index + 1)]['id'] === T_STRING;
    }

    /**
     * Index of the nearest token carrying something, walking either way
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index to start at
     * @param int $step Direction to walk in
     * @return int
     */
    protected static function significant(array $tokens, int $index, int $step = 1): int
    {
        $count = count($tokens);

        while ($index >= 0 && $index < $count && in_array($tokens[$index]['id'], self::TRIVIA, true)) {
            $index += $step;
        }

        return max(0, min($index, $count - 1));
    }

    /**
     * Index of the bracket an attribute closes at, so that it can be stepped over whole
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the attribute opens at
     * @return int
     */
    protected static function attribute(array $tokens, int $index): int
    {
        $count = count($tokens);
        $open = 1;

        for ($at = $index + 1; $at < $count; $at++) {
            $text = $tokens[$at]['text'];

            // An attribute may hold another one, and each opens a bracket of its own
            if ($text === '[' || $tokens[$at]['id'] === T_ATTRIBUTE) {
                $open++;
            } elseif ($text === ']' && --$open === 0) {
                return $at;
            }
        }

        return $count - 1;
    }

    /**
     * Index of the semicolon a statement ends at
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the statement opens at
     * @return int
     */
    protected static function statement(array $tokens, int $index): int
    {
        $count = count($tokens);
        $open = 0;

        for ($at = $index; $at < $count; $at++) {
            $text = $tokens[$at]['text'];

            if (in_array($text, ['(', '['], true)) {
                $open++;
            } elseif (in_array($text, [')', ']'], true)) {
                $open--;
            } elseif ($text === ';' && $open === 0) {
                return $at;
            }
        }

        return $count - 1;
    }

    /**
     * Index of the brace or semicolon a method declaration ends at
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the method opens at
     * @return int
     */
    protected static function closing(array $tokens, int $index): int
    {
        $count = count($tokens);
        $open = 0;
        $opened = false;

        for ($at = $index; $at < $count; $at++) {
            $text = $tokens[$at]['text'];

            if ($text === '(') {
                $open++;
                $opened = true;
            } elseif ($text === ')') {
                $open--;
            } elseif ($opened && $open === 0 && ($text === '{' || $text === ';')) {
                return $at;
            }
        }

        return $count - 1;
    }

    /**
     * Declaration line of a type, up to the brace its body opens with
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the type opens at
     * @return string
     */
    protected static function header(array $tokens, int $index): string
    {
        $count = count($tokens);
        $start = self::modifiers($tokens, $index);
        $end = $count;

        for ($at = $index; $at < $count; $at++) {
            if ($tokens[$at]['text'] === '{') {
                $end = $at;

                break;
            }
        }

        return self::spacing(self::between($tokens, $start, $end));
    }

    /**
     * Index a declaration opens at once its modifiers are counted in
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index of the keyword the declaration is made with
     * @return int
     */
    protected static function modifiers(array $tokens, int $index): int
    {
        $start = $index;

        for ($back = $index - 1; $back >= 0; $back--) {
            if (in_array($tokens[$back]['id'], self::TRIVIA, true)) {
                continue;
            }

            if (!in_array($tokens[$back]['id'], self::MODIFIERS, true)) {
                break;
            }

            $start = $back;
        }

        return $start;
    }

    /**
     * Signature of a method, from its modifiers through to its return type
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the method opens at
     * @return string
     */
    protected static function signature(array $tokens, int $index): string
    {
        // Back over the modifiers, so that a reader is told how a method is called before its name
        $start = self::modifiers($tokens, $index);
        $end = self::closing($tokens, $index);

        return self::spacing(self::between($tokens, $start, $end));
    }

    /**
     * Source between two tokens, with everything carrying nothing turned into a space
     *
     * @param array $tokens Tokens of the source
     * @param int $start Index to read from
     * @param int $end Index to read up to, itself left out
     * @return string
     */
    protected static function between(array $tokens, int $start, int $end): string
    {
        $text = '';

        for ($at = $start; $at < $end; $at++) {
            // An attribute says nothing about how a member is read or called
            if ($tokens[$at]['id'] === T_ATTRIBUTE) {
                $at = self::attribute($tokens, $at);
                $text .= ' ';

                continue;
            }

            $text .= in_array($tokens[$at]['id'], self::TRIVIA, true) ? ' ' : $tokens[$at]['text'];
        }

        return $text;
    }

    /**
     * Names a single statement declares, as one statement may declare several
     *
     * @param array $tokens Tokens of the source
     * @param int $index Index the statement opens at
     * @param int $end Index the statement ends at
     * @return array
     */
    protected static function names(array $tokens, int $index, int $end): array
    {
        $names = [];
        $open = 0;

        for ($at = $index + 1; $at < $end; $at++) {
            $text = $tokens[$at]['text'];

            if (in_array($text, ['(', '['], true)) {
                $open++;
            } elseif (in_array($text, [')', ']'], true)) {
                $open--;
            }

            if ($open !== 0 || $tokens[$at]['id'] !== T_STRING) {
                continue;
            }

            // A name is one that is given a value, or one that stands on its own before a comma
            $next = self::significant($tokens, $at + 1);
            $following = $tokens[$next]['text'];

            if ($next >= $end || in_array($following, ['=', ','], true)) {
                $names[] = $text;
            }
        }

        return $names === [] ? [$tokens[self::significant($tokens, $index + 1)]['text']] : $names;
    }

    /**
     * Declaration written out on one line, however the source had it wrapped
     *
     * Spacing is only ever taken away, never put in, as a token like a double
     * colon arrives with nothing around it and would be broken apart by spacing it.
     *
     * @param string $text Declaration to tidy
     * @return string
     */
    protected static function spacing(string $text): string
    {
        $text = (string) preg_replace('/\s+/', ' ', $text);
        $text = (string) preg_replace('/\(\s+/', '(', $text);
        $text = (string) preg_replace('/\s+\)/', ')', $text);
        $text = (string) preg_replace('/,\s*\)/', ')', $text);
        $text = (string) preg_replace('/\s+,/', ',', $text);
        $text = (string) preg_replace('/,(?=\S)/', ', ', $text);

        return trim($text);
    }
}

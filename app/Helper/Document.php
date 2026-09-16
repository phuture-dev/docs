<?php

namespace Phuture\App\Helper;

class Document
{
    /**
     * Longest title taken out of a document
     */
    public const TITLE_LENGTH = 255;

    /**
     * File extensions a page can be written in, in order of preference
     */
    public const EXTENSIONS = ['md', 'html'];

    /**
     * Names a document is served under when a url points at the folder holding it
     */
    public const DEFAULT_NAMES = ['index', 'readme'];

    /**
     * Contents of the files read so far, keyed by path
     */
    private static array $contents = [];

    /**
     * Path of the first of the given file names present in a directory, matched case insensitively
     *
     * @param string $directory Directory to look into
     * @param array $names File names to look for, in order of preference
     * @return string|null
     */
    public static function find(string $directory, array $names): ?string
    {
        if (!is_dir($directory)) {
            return null;
        }

        $files = [];

        foreach ((array) scandir($directory) as $file) {
            if (is_file($directory . DS . $file)) {
                $files[strtolower($file)] = $file;
            }
        }

        foreach ($names as $name) {
            $name = strtolower($name);

            if (isset($files[$name])) {
                return $directory . DS . $files[$name];
            }
        }

        return null;
    }

    /**
     * File names to look for a document under, one per supported extension
     *
     * @param string|array $names Names of the documents, without their extension
     * @return array
     */
    public static function fileNames(string|array $names): array
    {
        $files = [];

        foreach ((array) $names as $name) {
            foreach (self::EXTENSIONS as $extension) {
                $files[] = $name . '.' . $extension;
            }
        }

        return $files;
    }

    /**
     * Raw contents of a file, or null when it cannot be read
     *
     * @param string $path Path of the file
     * @return string|null
     */
    public static function contents(string $path): ?string
    {
        if (isset(self::$contents[$path])) {
            return self::$contents[$path];
        }

        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return self::$contents[$path] = ($contents === false ? null : $contents);
    }

    /**
     * Contents of a document as HTML, or null when it cannot be read or parsed
     *
     * @param string $path Path of the document
     * @return string|null
     */
    public static function file(string $path): ?string
    {
        return match (self::extension($path)) {
            'md' => Markdown::file($path),
            'html' => Html::file($path),
            default => null,
        };
    }

    /**
     * Title of a document, taken from its own contents
     *
     * @param string $path Path of the document
     * @param int $length Maximum length of the title
     * @return string|null
     */
    public static function title(string $path, int $length = self::TITLE_LENGTH): ?string
    {
        return match (self::extension($path)) {
            'md' => Markdown::title($path, $length),
            'html' => Html::title($path, $length),
            default => null,
        };
    }

    /**
     * Extension of a file, lowercased
     *
     * @param string $path Path of the file
     * @return string
     */
    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Cut a title down to length, without leaving a dangling word
     *
     * @param string $title Title to shorten
     * @param int $length Maximum length of the title
     * @return string
     */
    public static function shorten(string $title, int $length = self::TITLE_LENGTH): string
    {
        $title = trim(preg_replace('/\s+/u', ' ', $title) ?? $title);

        if (mb_strlen($title) <= $length) {
            return $title;
        }

        $title = mb_substr($title, 0, $length);
        $space = mb_strrpos($title, ' ');

        return rtrim($space !== false ? mb_substr($title, 0, $space) : $title);
    }
}

# Unit

`Phuture\Coherence\Enum\Unit`

```php
enum Unit
```

Enumeration of all supported measurement units for unit conversion.

Each case represents a specific unit of measurement. Use these cases with
\Phuture\Coherence\Numbers::convert() to convert a value from one unit to another
within the same measurement category (for example, from Celsius to Fahrenheit,
or from Kilometer to Mile).

The units are organized into the following categories:

- **Temperature**: Celsius, Fahrenheit, Kelvin, Rankine
- **Distance**: Meter, Millimeter, Centimeter, Decimeter, Kilometer, Inch, Foot, Yard, Mile, NauticalMile
- **Mass**: Kilogram, Gram, Milligram, Microgram, MetricTon, Pound, Ounce, Stone, UsTon, ImperialTon
- **Volume**: Liter, Milliliter, CubicMeter, GallonUs, QuartUs, PintUs, CupUs, FluidOunceUs, Tablespoon, Teaspoon
- **Time**: Second, Millisecond, Microsecond, Nanosecond, Minute, Hour, Day, Week
- **Area**: SquareMeter, SquareKilometer, Hectare, Acre, SquareFoot, SquareYard, SquareMile, SquareInch
- **Speed**: MeterPerSecond, KilometerPerHour, MilePerHour, Knot, FootPerSecond
- **Pressure**: Pascal, Kilopascal, Bar, Millibar, Atmosphere, Psi, Mmhg
- **Energy**: Joule, Kilojoule, Calorie, Kilocalorie, WattHour, KilowattHour, Btu, Electronvolt
- **Power**: Watt, Kilowatt, Megawatt, HorsepowerMechanical, HorsepowerMetric
- **Force**: Newton, Kilonewton, Dyne, PoundForce, KilogramForce
- **Electric Potential**: Volt, Millivolt, Kilovolt, Megavolt
- **Electric Current**: Ampere, Milliampere, Microampere, Kiloampere
- **Luminous Intensity**: Candela, Millicandela, Kilocandela

**Example:**
```php
use Phuture\Coherence\Enum\Unit;
use Phuture\Coherence\Numbers;

$fahrenheit = Numbers::convert(100, Unit::Celsius, Unit::Fahrenheit);
// Returns: '212.0000000000'

$miles = Numbers::convert(1, Unit::Kilometer, Unit::Mile);
// Returns: '0.6213711922'
```

## Cases

### `Acre`

```php
case Acre
```

Acre, an imperial unit of area equal to approximately 4046.8564224 square meters.

### `Ampere`

```php
case Ampere
```

Ampere, the base SI unit of electric current.

### `Atmosphere`

```php
case Atmosphere
```

Standard atmosphere, a reference pressure equal to 101325 pascals.

### `Bar`

```php
case Bar
```

Bar, a metric unit of pressure equal to 100000 pascals.

### `Btu`

```php
case Btu
```

British thermal unit (BTU), approximately 1055.06 joules.

### `Calorie`

```php
case Calorie
```

Calorie (thermochemical), approximately 4.184 joules.

### `Candela`

```php
case Candela
```

Candela, the base SI unit of luminous intensity.

### `Celsius`

```php
case Celsius
```

Degrees Celsius, the standard metric temperature scale where water freezes at 0 and boils at 100.

### `Centimeter`

```php
case Centimeter
```

Centimeter, one hundredth of a meter (0.01 meters).

### `CubicMeter`

```php
case CubicMeter
```

Cubic meter, a metric unit of volume equal to 1000 liters.

### `CupUs`

```php
case CupUs
```

US cup, one sixteenth of a US gallon (approximately 0.2365882365 liters).

### `Day`

```php
case Day
```

Day, 86400 seconds (24 hours).

### `Decimeter`

```php
case Decimeter
```

Decimeter, one tenth of a meter (0.1 meters).

### `Dyne`

```php
case Dyne
```

Dyne, a CGS unit of force equal to 0.00001 newtons.

### `Electronvolt`

```php
case Electronvolt
```

Electronvolt, a tiny unit of energy used in particle physics (approximately 1.602e-19 joules).

### `Fahrenheit`

```php
case Fahrenheit
```

Degrees Fahrenheit, the temperature scale where water freezes at 32 and boils at 212.

### `FluidOunceUs`

```php
case FluidOunceUs
```

US fluid ounce, a US unit of volume equal to approximately 0.029573529 liters.

### `Foot`

```php
case Foot
```

Foot, an imperial unit of length equal to exactly 12 inches (0.3048 meters).

### `FootPerSecond`

```php
case FootPerSecond
```

Feet per second, an imperial speed unit equal to exactly 0.3048 meters per second.

### `GallonUs`

```php
case GallonUs
```

US gallon, a US customary unit of volume equal to approximately 3.785411784 liters.

### `Gram`

```php
case Gram
```

Gram, one thousandth of a kilogram (0.001 kilograms).

### `Hectare`

```php
case Hectare
```

Hectare, a metric unit of area equal to 10000 square meters.

### `HorsepowerMechanical`

```php
case HorsepowerMechanical
```

Mechanical horsepower, an imperial power unit equal to approximately 745.7 watts.

### `HorsepowerMetric`

```php
case HorsepowerMetric
```

Metric horsepower (PS), equal to approximately 735.499 watts.

### `Hour`

```php
case Hour
```

Hour, 3600 seconds (60 minutes).

### `ImperialTon`

```php
case ImperialTon
```

Imperial ton (long ton), equal to 2240 pounds (approximately 1016.0469088 kilograms).

### `Inch`

```php
case Inch
```

Inch, an imperial unit of length equal to exactly 0.0254 meters.

### `Joule`

```php
case Joule
```

Joule, the base SI unit of energy.

### `Kelvin`

```php
case Kelvin
```

Kelvin, the absolute temperature scale starting at absolute zero (-273.15 degrees Celsius).

### `Kiloampere`

```php
case Kiloampere
```

Kiloampere, one thousand amperes.

### `Kilocalorie`

```php
case Kilocalorie
```

Kilocalorie (food calorie), approximately 4184 joules.

### `Kilocandela`

```php
case Kilocandela
```

Kilocandela, one thousand candelas.

### `Kilogram`

```php
case Kilogram
```

Kilogram, the base unit of mass in the metric system.

### `KilogramForce`

```php
case KilogramForce
```

Kilogram-force, a gravitational metric unit of force equal to approximately 9.80665 newtons.

### `Kilojoule`

```php
case Kilojoule
```

Kilojoule, one thousand joules.

### `Kilometer`

```php
case Kilometer
```

Kilometer, one thousand meters (1000 meters).

### `KilometerPerHour`

```php
case KilometerPerHour
```

Kilometers per hour, a metric speed unit equal to approximately 0.277778 meters per second.

### `Kilonewton`

```php
case Kilonewton
```

Kilonewton, one thousand newtons.

### `Kilopascal`

```php
case Kilopascal
```

Kilopascal, one thousand pascals.

### `Kilovolt`

```php
case Kilovolt
```

Kilovolt, one thousand volts.

### `Kilowatt`

```php
case Kilowatt
```

Kilowatt, one thousand watts.

### `KilowattHour`

```php
case KilowattHour
```

Kilowatt-hour, a unit of energy equal to 3600000 joules.

### `Knot`

```php
case Knot
```

Knot, a speed unit equal to one nautical mile per hour (approximately 0.514444 meters per second).

### `Liter`

```php
case Liter
```

Liter, the base metric unit of volume.

### `Megavolt`

```php
case Megavolt
```

Megavolt, one million volts.

### `Megawatt`

```php
case Megawatt
```

Megawatt, one million watts.

### `Meter`

```php
case Meter
```

Meter, the base unit of length in the metric system.

### `MeterPerSecond`

```php
case MeterPerSecond
```

Meters per second, the base metric unit of speed.

### `MetricTon`

```php
case MetricTon
```

Metric ton, one thousand kilograms.

### `Microampere`

```php
case Microampere
```

Microampere, one millionth of an ampere (0.000001 amperes).

### `Microgram`

```php
case Microgram
```

Microgram, one millionth of a gram (0.000000001 kilograms).

### `Microsecond`

```php
case Microsecond
```

Microsecond, one millionth of a second (0.000001 seconds).

### `Mile`

```php
case Mile
```

Mile, an imperial unit of distance equal to 5280 feet (1609.344 meters).

### `MilePerHour`

```php
case MilePerHour
```

Miles per hour, an imperial speed unit equal to approximately 0.44704 meters per second.

### `Milliampere`

```php
case Milliampere
```

Milliampere, one thousandth of an ampere (0.001 amperes).

### `Millibar`

```php
case Millibar
```

Millibar, one thousandth of a bar (100 pascals).

### `Millicandela`

```php
case Millicandela
```

Millicandela, one thousandth of a candela (0.001 candelas).

### `Milligram`

```php
case Milligram
```

Milligram, one thousandth of a gram (0.000001 kilograms).

### `Milliliter`

```php
case Milliliter
```

Milliliter, one thousandth of a liter (0.001 liters).

### `Millimeter`

```php
case Millimeter
```

Millimeter, one thousandth of a meter (0.001 meters).

### `Millisecond`

```php
case Millisecond
```

Millisecond, one thousandth of a second (0.001 seconds).

### `Millivolt`

```php
case Millivolt
```

Millivolt, one thousandth of a volt (0.001 volts).

### `Minute`

```php
case Minute
```

Minute, 60 seconds.

### `Mmhg`

```php
case Mmhg
```

Millimeters of mercury (mmHg), a pressure unit equal to approximately 133.322 pascals.

### `Nanosecond`

```php
case Nanosecond
```

Nanosecond, one billionth of a second (0.000000001 seconds).

### `NauticalMile`

```php
case NauticalMile
```

Nautical mile, a unit used in air and sea navigation equal to exactly 1852 meters.

### `Newton`

```php
case Newton
```

Newton, the base SI unit of force.

### `Ounce`

```php
case Ounce
```

Ounce, an imperial unit of mass equal to approximately 0.028349523 kilograms (1/16 of a pound).

### `Pascal`

```php
case Pascal
```

Pascal, the base SI unit of pressure (one newton per square meter).

### `PintUs`

```php
case PintUs
```

US pint, one eighth of a US gallon (approximately 0.473176473 liters).

### `Pound`

```php
case Pound
```

Pound, an imperial unit of mass equal to approximately 0.45359237 kilograms.

### `PoundForce`

```php
case PoundForce
```

Pound-force, an imperial unit of force equal to approximately 4.448222 newtons.

### `Psi`

```php
case Psi
```

Pounds per square inch (PSI), an imperial pressure unit equal to approximately 6894.757 pascals.

### `QuartUs`

```php
case QuartUs
```

US quart, one fourth of a US gallon (approximately 0.946352946 liters).

### `Rankine`

```php
case Rankine
```

Degrees Rankine, an absolute temperature scale where zero is absolute zero and each degree
equals one degree Fahrenheit.

### `Second`

```php
case Second
```

Second, the base unit of time.

### `SquareFoot`

```php
case SquareFoot
```

Square foot, an imperial unit of area equal to approximately 0.09290304 square meters.

### `SquareInch`

```php
case SquareInch
```

Square inch, an imperial unit of area equal to approximately 0.00064516 square meters.

### `SquareKilometer`

```php
case SquareKilometer
```

Square kilometer, one million square meters.

### `SquareMeter`

```php
case SquareMeter
```

Square meter, the base metric unit of area.

### `SquareMile`

```php
case SquareMile
```

Square mile, an imperial unit of area equal to approximately 2589988.110336 square meters.

### `SquareYard`

```php
case SquareYard
```

Square yard, an imperial unit of area equal to approximately 0.83612736 square meters.

### `Stone`

```php
case Stone
```

Stone, an imperial unit of mass equal to 14 pounds (approximately 6.350293 kilograms).

### `Tablespoon`

```php
case Tablespoon
```

Tablespoon, a culinary unit of volume equal to approximately 0.014786764 liters.

### `Teaspoon`

```php
case Teaspoon
```

Teaspoon, a culinary unit of volume equal to approximately 0.004928921 liters.

### `UsTon`

```php
case UsTon
```

US ton (short ton), equal to 2000 pounds (approximately 907.18474 kilograms).

### `Volt`

```php
case Volt
```

Volt, the base SI unit of electric potential.

### `Watt`

```php
case Watt
```

Watt, the base SI unit of power (one joule per second).

### `WattHour`

```php
case WattHour
```

Watt-hour, a unit of energy equal to 3600 joules.

### `Week`

```php
case Week
```

Week, 604800 seconds (7 days).

### `Yard`

```php
case Yard
```

Yard, an imperial unit of length equal to exactly 3 feet (0.9144 meters).

## Methods

### `category()`

```php
public function category(): string
```

Returns the measurement category this unit belongs to.

Each unit belongs to exactly one category such as 'temperature', 'distance', or 'mass'.
Conversions are only valid between units in the same category.

**Example:**
```php
use Phuture\Coherence\Enum\Unit;

Unit::Celsius->category(); // 'temperature'
Unit::Kilometer->category(); // 'distance'
Unit::Kilogram->category(); // 'mass'
```

**Returns** `string` — The category name

### `factor()`

```php
public function factor(): string
```

Returns the multiplication factor to convert this unit to the base unit of its category.

The factor is a numeric string suitable for use with BCMath functions. Multiplying a value
in this unit by the factor produces the equivalent value in the base unit of the category.
For temperature units, this factor is not used because temperature conversion requires
offset-based formulas handled separately by \Phuture\Coherence\Numbers::convertTemperature().

**Example:**
```php
use Phuture\Coherence\Enum\Unit;

Unit::Kilometer->factor(); // '1000' (1 km = 1000 meters)
Unit::Gram->factor(); // '0.001' (1 g = 0.001 kilograms)
Unit::Hour->factor(); // '3600' (1 hour = 3600 seconds)
```

**Returns** `string` — The conversion factor as a BCMath-compatible numeric string

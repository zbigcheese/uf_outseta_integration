<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration;

// ADVANCED DEBUGGING BLOCK
// We will check if PHP can find the file and the interface at runtime.
echo "<h1>Debugging UfOutsetaIntegration...</h1>";

// Check if the interface file exists and is readable
$interfacePath = __DIR__ . '/../../../../userfrosting/framework/src/Sprinkle/Sprinkle.php';
echo "Checking for file at: " . realpath($interfacePath) . "<br>";
echo "File exists? ";
var_dump(file_exists($interfacePath));
echo "File is readable? ";
var_dump(is_readable($interfacePath));

// Check if PHP thinks the interface exists (this will trigger the autoloader)
echo "Interface exists before 'use' statement? ";
var_dump(interface_exists(\UserFrosting\Sprinkle\Sprinkle::class, true));

// Stop execution so we can see the output
die("<br>--- End of Debugging ---");
// END OF DEBUGGING BLOCK


use UserFrosting\Sprinkle\Sprinkle;

class UfOutsetaIntegration implements Sprinkle
{
    public function getBootstrapper(): string
    {
        return \Zbigcheese\Sprinkles\UfOutsetaIntegration\Sprinkle\UfOutsetaIntegrationBootstrapper::class;
    }
}
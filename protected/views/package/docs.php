<ul>
<?php
    $packages=Package::model()->findAll();
    foreach($packages as $package) {
        echo "<li>$package->name<ul>";
        foreach($package->versionsDeployed as $version) {
            echo "<li>$version->number (<a href=''>English</a>)</li>";
        }
        echo "</ul></li>";
    }
?>    
</ul>

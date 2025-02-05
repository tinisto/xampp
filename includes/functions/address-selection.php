<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Fetch countries from the database
function getCountries()
{
    global $connection;
    $query = "SELECT id_country, country_name FROM countries ORDER BY country_name";
    $result = mysqli_query($connection, $query);
    $countries = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $countries[] = $row;
    }
    return $countries;
}

// Fetch regions based on country
function getRegionsByCountry($countryId)
{
    global $connection;
    $query = "SELECT id_region, region_name FROM regions WHERE id_country = ? ORDER BY region_name";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $countryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $regions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $regions[] = $row;
    }

    return $regions;
}

// Fetch areas based on region
function getAreasByRegion($regionId)
{
    global $connection;
    $query = "SELECT id_area, name FROM areas WHERE id_region = ? ORDER BY name";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $regionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $areas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $areas[] = $row;
    }
    return $areas;
}

// Fetch towns based on area
function getTownsByArea($areaId)
{
    global $connection;
    $query = "SELECT id_town, name FROM towns WHERE id_area = ? ORDER BY name";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $areaId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $towns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $towns[] = $row;
    }
    return $towns;
}

// Initialize variables for countries, regions, areas, and towns
$countries = getCountries();
$regions = $areas = $towns = [];

// If the page is receiving AJAX requests to fetch regions, areas, or towns, handle it here
if (isset($_GET['action'])) {
    $response = '';

    // Fetch regions based on country
    if ($_GET['action'] == 'fetch_regions' && isset($_GET['country_id'])) {
        $regions = getRegionsByCountry($_GET['country_id']);
        foreach ($regions as $region) {
            $response .= '<option value="' . $region['id_region'] . '">' . $region['region_name'] . '</option>';
        }
        echo $response;
        exit;
    }

    // Fetch areas based on region
    if ($_GET['action'] == 'fetch_areas' && isset($_GET['region_id'])) {
        $areas = getAreasByRegion($_GET['region_id']);
        foreach ($areas as $area) {
            $response .= '<option value="' . $area['id_area'] . '">' . $area['name'] . '</option>';
        }
        echo $response;
        exit;
    }

    // Fetch towns based on area
    if ($_GET['action'] == 'fetch_towns' && isset($_GET['area_id'])) {
        $towns = getTownsByArea($_GET['area_id']);
        foreach ($towns as $town) {
            $response .= '<option value="' . $town['id_town'] . '">' . $town['name'] . '</option>';
        }
        echo $response;
        exit;
    }
}
?>

<div class="row">
    <!-- Country Select -->
    <div class="col-md-3 mb-3">
        <select class="form-select" id="country" name="country" required>
            <option value="">Выберите страну</option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?= $country['id_country']; ?>"
                    <?= ($selected_country == $country['id_country']) ? 'selected' : ''; ?>>
                    <?= $country['country_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>



    <div class="col-md-3 mb-3">
        <select class="form-select" id="region" name="region" required>
            <!-- Only show the placeholder if no region is selected -->
            <?php if (empty($selected_region)) : ?>
                <option value="">Выберите регион</option>
            <?php endif; ?>

            <?php foreach ($regions as $region) : ?>
                <option value="<?= $region['id_region']; ?>"
                    <?php if ((int)$selected_region === (int)$region['id_region']) : ?>
                    selected
                    <?php endif; ?>>
                    <?= $region['region_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>



    <!-- Area Select -->
    <div class="col-md-3 mb-3">
        <select class="form-select" id="area" name="area" required>
            <option value="">Выберите район</option>
            <?php if (!empty($areas)) : ?>
                <?php foreach ($areas as $area) : ?>
                    <option value="<?= $area['id_area']; ?>"
                        <?= ($selected_area == $area['id_area']) ? 'selected' : ''; ?>>
                        <?= $area['name']; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Town Select -->
    <div class="col-md-3 mb-3">
        <select class="form-select" id="town" name="town" required>
            <option value="">Выберите город</option>
            <?php if (!empty($towns)) : ?>
                <?php foreach ($towns as $town) : ?>
                    <option value="<?= $town['id_town']; ?>"
                        <?= ($selected_town == $town['id_town']) ? 'selected' : ''; ?>>
                        <?= $town['name']; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>

<script>
    // Function to fetch and populate regions based on selected country
    function fetchRegions(countryId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?action=fetch_regions&country_id=' + countryId, true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById('region').innerHTML = '<option value="">Выберите регион</option>' + xhr.responseText;
                // Clear areas and towns if country is changed
                document.getElementById('area').innerHTML = '<option value="">Выберите район</option>';
                document.getElementById('town').innerHTML = '<option value="">Выберите город</option>';
                // Trigger region change event if region is already selected
                var regionSelect = document.getElementById('region');
                if (regionSelect.value) {
                    regionSelect.dispatchEvent(new Event('change'));
                }
            }
        };
        xhr.send();
    }

    // Function to fetch and populate areas based on selected region
    function fetchAreas(regionId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?action=fetch_areas&region_id=' + regionId, true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById('area').innerHTML = '<option value="">Выберите район</option>' + xhr.responseText;
                document.getElementById('town').innerHTML = '<option value="">Выберите город</option>';
                // Trigger area change event if area is already selected
                var areaSelect = document.getElementById('area');
                if (areaSelect.value) {
                    areaSelect.dispatchEvent(new Event('change'));
                }
            }
        };
        xhr.send();
    }

    // Function to fetch and populate towns based on selected area
    function fetchTowns(areaId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?action=fetch_towns&area_id=' + areaId, true);
        xhr.onload = function() {
            if (xhr.status == 200) {
                document.getElementById('town').innerHTML = '<option value="">Выберите город</option>' + xhr.responseText;
            }
        };
        xhr.send();
    }

    // Handle country change event
    document.getElementById('country').addEventListener('change', function() {
        var countryId = this.value;
        if (countryId) {
            fetchRegions(countryId);
        } else {
            document.getElementById('region').innerHTML = '<option value="">Выберите регион</option>';
            document.getElementById('area').innerHTML = '<option value="">Выберите район</option>';
            document.getElementById('town').innerHTML = '<option value="">Выберите город</option>';
        }
    });

    // Handle region change event
    document.getElementById('region').addEventListener('change', function() {
        var regionId = this.value;
        if (regionId) {
            fetchAreas(regionId);
        } else {
            document.getElementById('area').innerHTML = '<option value="">Выберите район</option>';
            document.getElementById('town').innerHTML = '<option value="">Выберите город</option>';
        }
    });

    // Handle area change event
    document.getElementById('area').addEventListener('change', function() {
        var areaId = this.value;
        if (areaId) {
            fetchTowns(areaId);
        } else {
            document.getElementById('town').innerHTML = '<option value="">Выберите город</option>';
        }
    });

    // Trigger the country change event on page load if country is already selected
    window.addEventListener('load', function() {
        var countrySelect = document.getElementById('country');
        if (countrySelect.value) {
            fetchRegions(countrySelect.value);
        }

        // Trigger the region change event if region is already selected
        var regionSelect = document.getElementById('region');
        if (regionSelect.value) {
            fetchAreas(regionSelect.value);
        }

        // Trigger the area change event if area is already selected
        var areaSelect = document.getElementById('area');
        if (areaSelect.value) {
            fetchTowns(areaSelect.value);
        }
    });

    console.log(document.getElementById('country').value);
    console.log(document.getElementById('region').value);
    console.log(document.getElementById('area').value);
    console.log(document.getElementById('town').value);
</script>
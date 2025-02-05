<?php
function extractEntityId($requestPath)
{
  // Split the path into segments
  $pathSegments = explode('/', trim($requestPath, '/'));

  // Check if the URL matches the expected structure for schools
  if (count($pathSegments) >= 2 && $pathSegments[0] === 'school') {
    // The school ID is the second segment
    $id_entity = $pathSegments[1];
    $entityType = 'school'; // Set the entity type
  }
  // Check if the URL matches the expected structure for universities
  elseif (count($pathSegments) >= 3 && $pathSegments[0] === 'vpo') {
    // The university name is the third segment
    $universityName = $pathSegments[2];
    // Use a function to get university ID based on the name
    $id_entity = getid_vpoByName($universityName);
    $entityType = 'university'; // Set the entity type
  }
  // Check if the URL matches the expected structure for spo
  elseif (count($pathSegments) >= 3 && $pathSegments[0] === 'spo') {
    // The college name is the third segment
    $collegeName = $pathSegments[2];
    // Use a function to get college ID based on the name
    $id_entity = getCollegeIdByName($collegeName);
    $entityType = 'college'; // Set the entity type
  } else {
    // Handle the case where the URL structure doesn't match
    $id_entity = null;
    $entityType = null; // Set entity type to null
  }

  return ['id_entity' => $id_entity, 'entityType' => $entityType];
}

function getid_vpoByName($universityName)
{
  // Add your logic to fetch university ID based on the name
  // For example, query the database using $universityName
  // Replace the following line with your actual logic
  return 123; // Replace with the actual ID value
}

function getCollegeIdByName($collegeName)
{
  // Add your logic to fetch college ID based on the name
  // For example, query the database using $collegeName
  // Replace the following line with your actual logic
  return 456; // Replace with the actual ID value
}

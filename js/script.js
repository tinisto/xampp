document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM is ready");

  // Get all region links on the page
  const regionLinks = document.querySelectorAll(".region-link");

  // Add click event listener to each region link
  regionLinks.forEach(function (regionLink) {
    regionLink.addEventListener("click", function (event) {
      // Prevent the default behavior of the link
      event.preventDefault();

      // Get the clicked region element
      const clickedRegion = regionLink.closest(".region");

      // Fetch areas for the selected region using AJAX
      fetch("areas.php?region_id=" + regionLink.getAttribute("data-region-id"))
        .then((response) => response.text())
        .then((data) => {
          // Update the content of the clicked region with the fetched areas
          clickedRegion.innerHTML += data;

          // Disable the link for the clicked region
          regionLink.classList.add("disabled");

          // Add a class to the body to indicate a region is clicked

          // Toggle classes to hide other regions
          const allRegions = document.querySelectorAll(".region");
          allRegions.forEach(function (otherRegion) {
            if (otherRegion !== clickedRegion) {
              // Toggle the d-none class instead of hidden
              otherRegion.classList.toggle("d-none");
              console.log("Hidden class toggled");
            }
          });

          // Show the "Show All Regions" link
          const showAllRegionsLink =
            document.getElementById("showAllRegionsLink");
          showAllRegionsLink.classList.remove("d-none");

          // Hide the heading "Регионы России"
          const heading = document.querySelector(
            ".text-5xl.font-bold.mb-4.text-center"
          );
          heading.classList.add("d-none");
        })
        .catch((error) => console.error("Error fetching areas:", error));
    });
  });

  // Add event listener for the "Show All Regions" link
  const showAllRegionsLink = document.getElementById("showAllRegionsLink");
  showAllRegionsLink.addEventListener("click", function (event) {
    event.preventDefault();

    // Clear the content of the clicked region
    const clickedRegion = document.querySelector(".region:not(.d-none)");
    clickedRegion.innerHTML = "";

    // Enable all region links
    regionLinks.forEach(function (link) {
      link.classList.remove("disabled");
    });

    // Show all regions by removing the "d-none" class
    const allRegions = document.querySelectorAll(".region");
    allRegions.forEach(function (region) {
      region.classList.remove("d-none");
    });

    // Hide the "Show All Regions" link again
    showAllRegionsLink.classList.add("d-none");

    // Show the heading "Регионы России"
    const heading = document.querySelector(
      ".text-5xl.font-bold.mb-4.text-center"
    );
    heading.classList.remove("d-none");
  });
});

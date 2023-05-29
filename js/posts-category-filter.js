// Wait for the DOM to be fully loaded before running the code
document.addEventListener('DOMContentLoaded', function() {
    // Get the category select and posts elements
    const categorySelect = document.getElementById('category-select');
    const categoryPosts = document.getElementById('category-posts');

    // Listen for changes to the category select element
    categorySelect.addEventListener('change', function() {
        // Update the posts when the category is changed
        updatePosts(this.value, 1);
    });

    // Listen for clicks on the category posts element
    categoryPosts.addEventListener('click', function(event) {
        // Check if a pagination link was clicked
        if (event.target.classList.contains('page-link')) {
            // Prevent the default link behavior
            event.preventDefault();
            // Get the page number from the clicked link
            const page = event.target.dataset.page;
            // Get the current category ID from the category select element
            const categoryId = categorySelect.value;
            // Update the posts with the new page number
            updatePosts(categoryId, page);
        }
    });

    /**
     * Update the posts for a given category and page number.
     *
     * @param {number} categoryId The ID of the selected category.
     * @param {number} page The current page number.
     */
    function updatePosts(categoryId, page) {
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();
        // Set up the AJAX request to the WordPress admin-ajax.php file
        xhr.open('POST', '/wp-admin/admin-ajax.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        // Listen for the AJAX response
        xhr.onload = function() {
            // Check if the request was successful
            if (xhr.status === 200) {
                // Update the category posts with the response HTML
                categoryPosts.innerHTML = xhr.responseText;
            }
        };
        // Send the AJAX request with the action and data as URL-encoded parameters
        xhr.send(encodeURI('action=get_category_posts&category_id=' + categoryId + '&page=' + page));
    }

    // Set the initial value of the category select element to "Media"
    for (let i = 0; i < categorySelect.options.length; i++) {
        if (categorySelect.options[i].text === 'Media') {
            categorySelect.selectedIndex = i;
            break;
        }
    }

    // Trigger the change event on the category select element to update the posts
    var event = new Event('change');
    categorySelect.dispatchEvent(event);
});

function validateForm() {
    // error handling
    var errorCounter = 0;

    $(".required").each(function(i, obj) {

        if($(this).val() === ''){
            $(this).parent().addClass("has-error");
            errorCounter++;
        } else{ 
            $(this).parent().removeClass("has-error"); 
        }

    });
    
    return errorCounter;

}

// Function to format number with commas and ensure two decimal places
function formatNumber(input) {
    let value = input.value.trim(); // Trim any leading or trailing whitespace
    // Remove any commas
    value = value.replace(/,/g, '');
    
    // Check if the value is numeric
    if (!isNaN(value) && value !== '') {
        // Convert value to number
        let num = parseFloat(value).toFixed(2); // Ensure two decimal places
        
        // Format number with commas
        let parts = num.toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        let formattedNum = parts.join('.');
        
        // Update input value
        input.value = formattedNum;
    } else {
        // Handle non-numeric or empty input if needed
        input.value = '';
    }
}


function validateNoSpecialChars(inputElement) {
    // Listen for input events in the given input element
    $(inputElement).on("input", function() {
        // Get the entered text
        var enteredText = $(this).val();

        // Define a regular expression to allow hyphens and apostrophes
        var allowedCharacters = /^[a-zA-Z'\- ]*$/;

        // Check if the entered text contains only allowed characters
        if (allowedCharacters.test(enteredText)) {
            // Remove the error-highlight class if the input is valid
            $(this).removeClass("error-highlight");
        } else {
            // Clear the input field, add the error-highlight class, and display an error message
            $(this).val(""); // Clear the input field
            $(this).addClass("error-highlight");

            // Display an error message (you can customize this message)
            alert("Special characters and numbers are not allowed.");
        }
    });
}

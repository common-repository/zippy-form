(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

document.addEventListener('DOMContentLoaded', function() {
	
	


	document.querySelectorAll('.zippy-phone-number').forEach(function(element) {
		element.addEventListener('keydown', function(e) {
			// Allow: backspace, delete, tab, escape, enter, and .
			if ([46, 8, 9, 27, 13, 110].includes(e.keyCode) ||
				// Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) ||
				// Allow: home, end, left, right
				(e.keyCode >= 35 && e.keyCode <= 39)) {
				// Let it happen, don't do anything
				return;
			}
	
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
	});
	
	});
	 
  

function showStep(stepNumber) {
	document.querySelectorAll('.tab').forEach(step => {
	  step.style.display = 'none';
	});
	document.getElementById(`step${stepNumber}`).style.display = 'block';
	fixStepIndicator(stepNumber);
  }
  
  function nextStep(stepNumber) {
	
	var currentStep = stepNumber - 1;
	
	var cStep = currentStep;
	var j = cStep - 1;
	var x, y, i;
	var valid = true;
	x = document.getElementsByClassName("tab");
	y = x[j].getElementsByClassName("require");
	var errorDivs = document.querySelectorAll(".error-message");
	errorDivs.forEach(function (errorDiv) {
		errorDiv.remove();
	});

	// If the valid status is true, mark the step as finished and valid:
	var finalStep = document.getElementById("totalStep").value;
	
		if(finalStep==1){
			return valid;
		}else{

	  if (document.getElementsByClassName("step")[j].classList.contains('finish')) {
		// do some stuff
		  } else {
			
		document.getElementsByClassName("step")[j].className += " finish";
		
	  }
	}
	showStep(stepNumber);
	
  }
  
  function previousStep(stepNumber) {
	var i, x = document.getElementsByClassName("step");
	n = stepNumber-1;
	for (i = 0; i < x.length; i++) {
	  x[n].className = x[n].className.replace(" finish", "");
	  
	}
	showStep(stepNumber);
  }
  


  
  function fixStepIndicator(stepNumber) {
	// This function removes the "active" class of all steps...
	var i, x = document.getElementsByClassName("step");
	var progress = document.getElementById("progress-steps");
	var k = 100 / (x.length - 1);
	n = stepNumber - 1;
	
	for (i = 0; i < x.length; i++) {
	  x[i].className = x[i].className.replace(" active", "");
	  progress.style.width = k * n + "%";
	}
	//... and adds the "active" class on the current step:
	x[n].className += " active";
  }
  function restrictInvalidChars(inputElement) {
    // Get the input value
    var inputValue = inputElement.value;

    // Remove any occurrences of the letter "e" and negative sign "-"
    inputValue = inputValue.replace(/E/g, '');
    inputValue = inputValue.replace(/-/g, '');

    // Update the input value
    inputElement.value = inputValue;
}


jQuery(document).ready(function() {
	var invalidChars = ["-", "e", "+", "E"];

	jQuery("input[type='number']").on("keydown", function(e){ 
    if(invalidChars.includes(e.key)){
         e.preventDefault();
    }
});
var fileInput = jQuery('.zippy-file-input');
var droparea = jQuery('.zippy-file-upload');

// highlight drag area
fileInput.on('dragenter focus click', function() {
  droparea.addClass('is-active');
});

// back to normal state
fileInput.on('dragleave blur drop', function() {
  droparea.removeClass('is-active');
});

// change inner text
fileInput.on('change', function() {
  var filesCount = jQuery(this)[0].files.length;
  var textContainer =jQuery(this).prev();

  if (filesCount === 1) {
    // if single file is selected, show file name
    var fileName = jQuery(this).val().split('\\').pop();
    textContainer.text(fileName);
  } 
});

jQuery('.choices-multiple-remove-button').each(function() {
    // Get the value of the 'data-max-selected-options' attribute for each element
    var maxSelectedOptions = jQuery(this).attr("data-max-selected-options");

    // Initialize Choices for each element
    var choicesInstance = new Choices(this, {
        removeItemButton: true,
        maxItemCount: maxSelectedOptions,
        searchEnabled: false, // Disable the search functionality
		itemSelectText: '',
		placeholder: true,
		allowHTML: true 
    });
});

jQuery('.single-select').each(function() {
    // Get the value of the 'data-max-selected-options' attribute for each element
    // Initialize Choices for each element
    var choicesSingle = new Choices(this, {
        searchEnabled: false, // Disable the search functionality
		itemSelectText: '',
		allowHTML: true 
    });
});
	jQuery('.choices__input--cloned').prop('readonly', true);
	

	
	
	jQuery('.zippy-date-input').each(function () {
		var dateElement = jQuery(this).data("format");
	
		jQuery(this).flatpickr({
			enableTime: false,
			disableMobile: true,
			dateFormat: dateElement,
			//minDate: "today",
			// Add any other options specific to each input here
		});
	});


 
  jQuery(".time_24format").flatpickr({
	enableTime: true,
	  noCalendar: true,
	  dateFormat: "H:i",
	  time_24hr: true
  });
 
  // Prevent paste for zippy-decimal inputs
jQuery('.zippy-decimal').on('paste', function (e) {
	e.preventDefault();
  });
  
  // Disable alphabets for zippy-decimal inputs
  jQuery('.zippy-decimal').keydown(function (e) {
	// Get the occurrence of decimal operator
	var match = jQuery(this).val().match(/\./g);
	var invalidChars = ["-", "e", "+", "E"];
	if(invalidChars.includes(e.key)){
		e.preventDefault();
   }
	if (match != null) {
	  // Allow: backspace, delete, tab, escape and enter 
	  if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
		  // Allow: Ctrl+A
		  (e.keyCode == 65 && e.ctrlKey === true) ||
		  // Allow: home, end, left, right
		  (e.keyCode >= 35 && e.keyCode <= 39)) {
		// Let it happen, don't do anything
		return;
	  }  
	  // Ensure that it is a number and stop the keypress
	  else if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && (e.keyCode == 190)) {
		e.preventDefault();
	  }
	} else {
	  // Allow: backspace, delete, tab, escape, enter and .
	  if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		  // Allow: Ctrl+A
		  (e.keyCode == 65 && e.ctrlKey === true) ||
		  // Allow: home, end, left, right
		  (e.keyCode >= 35 && e.keyCode <= 39)) {
		// Let it happen, don't do anything
		return;
	  }
	  // Ensure that it is a number and stop the keypress
	  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	  }
	}
  });
  
  // Allow up to two decimal places value only
  jQuery('.zippy-decimal').keyup(function () {
	var decimalElement = jQuery(this).data("decimal");
	if (jQuery(this).val().indexOf('.') != -1) {
	  if (jQuery(this).val().split(".")[1].length > decimalElement) {
		if (isNaN(parseFloat(this.value))) return;
		this.value = parseFloat(this.value).toFixed(decimalElement);
	  }
	 
	 if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	  }
	  if (isNaN(e.key)) {
		e.preventDefault();
	  }
	}
	
  });





  });
  
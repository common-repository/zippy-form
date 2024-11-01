

document.addEventListener('DOMContentLoaded', function() {
	
	var inputFields = document.querySelectorAll('.require');
	var elements = document.getElementsByClassName("step");
	var currentStep = 1;
	for (var i = 0; i < elements.length; i++) {
		if (elements[i].classList.contains('active')) {
			var innerHTML = elements[i].innerHTML;
			currentStep = innerHTML; // or do something with the innerHTML
		}
	}

	inputFields.forEach(function(input) {
		input.addEventListener('blur', function() {
			validateFormBlur(input, currentStep);
		});
		input.addEventListener('change', function() {
			if (!input.classList.contains('choices-multiple-remove-button')) {
				validateFormBlur(input, currentStep);
			}
			
		});
		input.addEventListener('keyup', function() {
			validateFormBlur(input, currentStep);
		});
		if (input.type === 'file') {
			input.addEventListener('change', function() {
				validateFormChange(input, currentStep);
			});
		}
	});
	var choicesInputs = document.querySelectorAll('.choices-multiple-remove-button');

	// Iterate over the NodeList and attach the event listener to each element
	choicesInputs.forEach(function(choicesInput) {
		choicesInput.addEventListener('addItem', function(event) {
			var selectedOptions = event.detail.value;
			var mainParent = choicesInput.parentElement;
			var mainParentElement = mainParent.parentElement;
			var errorMessageElement = mainParentElement.nextElementSibling;
			var clonedInput = mainParent.querySelector('.choices__input--cloned');
	
			if (selectedOptions.length === 0) {
				// alert('test');
				var inputLabel = choicesInput.getAttribute('data-label');
				mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
				choicesInput.parentElement.classList.add("invalid");
				clonedInput.style.display='unset';
			} else {
				choicesInput.parentElement.classList.remove("invalid");
				if (errorMessageElement) {
					errorMessageElement.remove();
				}
				clonedInput.style.display='none';
			}
		});
	});

	var selectInputs = document.querySelectorAll('.single-select');

// Iterate over the NodeList and attach the event listener to each element
selectInputs.forEach(function(selectInput) {
    selectInput.addEventListener('change', function() {
        var selectedOption = selectInput.value;
        var mainParent = selectInput.parentElement;
        var mainParentElement = mainParent.parentElement;
        var errorMessageElement = mainParentElement.nextElementSibling;

        if (!selectedOption || selectedOption.trim() === '') {
            var inputLabel = selectInput.getAttribute('data-label');
            mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
            mainParent.classList.add("invalid");
        } else {
            mainParent.classList.remove("invalid");
            if (errorMessageElement) {
                errorMessageElement.remove();
            }
        }
    });
});
	
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

document.querySelectorAll('.zippy-date-input').forEach(function (element) {
    var dateElement = element.dataset.format;

    flatpickr(element, {
        enableTime: false,
        disableMobile: true,
        dateFormat: dateElement,
        //minDate: "today",
        // Add any other options specific to each input here
    });
});

flatpickr(".time_24format", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

flatpickr(".time_12format", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",
    time_24hr: false
});


});

function validateFormChange(input, currentStep) {
	var parent = input.parentElement;
	var nextSibling = input.nextElementSibling;

	if (parent && parent.nextElementSibling) {
		parent.nextElementSibling.remove()
	}
	if (input.type === 'file') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		var existingErrorMessages = input.parentElement.querySelectorAll('.error-message');
		existingErrorMessages.forEach(function(errorMessage) {
			errorMessage.remove();
		});
		if (input.files.length === 0) {
			// input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select a file</div>');


		} else if (input.files.length > 0) {
			if (parent && parent.nextElementSibling) {
				parent.nextElementSibling.remove()
			}

		} else {
			const maxFileSize = input.dataset.max * 1024 * 1024; // 2MB in bytes
			const selectedFile = input.files[0];

			if (selectedFile.size > maxFileSize) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">File size exceeds ' + y[i].dataset.max + 'MB limit</div>');

			} else {
				input.parentElement.classList.remove("invalid");
			}
		}
	}
}

function validateFormBlur(input, currentStep) {
	// This function deals with validation of the form fields
	// console.log('validate', currentStep);
	// var cStep = currentStep;
	// var j = cStep - 1;
	// var x, y, i;
	// valid = true;
	// x = document.getElementsByClassName("tab");
	// y = x[j].getElementsByClassName("require");
	// var errorDivs = document.querySelectorAll(".error-message");
	// errorDivs.forEach(function (errorDiv) {
	// 	errorDiv.remove();
	// });
	// A loop that checks every input field in the current tab:
	var parent = input.parentElement;
	var nextSibling = input.nextElementSibling;

	if (parent && parent.nextElementSibling) {
		parent.nextElementSibling.remove()
	}


	if (input.type === 'text' || input.type === 'tel' || input.type === 'textarea' || input.type === 'date' || input.type === 'time') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		if (input.classList.contains('zippy-decimal')) {
			if (input.value === '') {

				input.parentElement.classList.add("invalid");
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
				input.scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			} else if (
				isNaN(input.value) || // Check if it's not a valid number
				parseFloat(input.value) < parseFloat(input.min) ||
				parseFloat(input.value) > parseFloat(input.max)
			) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' value should be between ' + input.min + ' and ' + input.max + '.</div>');
				input.scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			} else {
				input.parentElement.classList.remove('invalid');
			}
		} else {
			if (input.value.trim() === '') {
				input.parentElement.className += " invalid";

				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');



			} else if (input.value.length < input.minLength || input.length > input.maxLength) {
				var inputLabel = document.getElementById(input.id).getAttribute('data-label');
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' must contain atleast ' + input.minLength + ' characters.</div>');


			} else {
				input.parentElement.classList.remove("invalid");

			}
		}
	} else if (input.type === 'number') {
		var minValue = input.min;
		var maxValue = input.max;
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		var decimal = document.getElementById(input.id).getAttribute('data-decimal');
		var enteredValue = parseFloat(input.value); // Convert input value to a number
		if (decimal > 0) {

			if (input.value === '') {
				input.parentElement.classList.add("invalid");
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');


			} else if (isNaN(enteredValue) || enteredValue < parseFloat(input.min) || enteredValue > parseFloat(input.max)) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + input.min + ' and ' + input.max + '.</div>');

			} else if (!isValidDecimal(enteredValue, decimal, minValue, maxValue)) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a number with a maximum of ' + decimal + ' decimal places.</div>');

			} else {
				input.parentElement.classList.remove("invalid");
			}
		} else {
			if (input.value === '') {
				input.parentElement.classList.add("invalid");
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');


			} else if (isNaN(enteredValue) || enteredValue < parseFloat(input.min) || enteredValue > parseFloat(input.max)) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + input.min + ' and ' + input.max + '.</div>');

			} else {
				input.parentElement.classList.remove("invalid");
			}
		}
	} else if (input.type === 'email') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		if (input.value === '') {
			input.parentElement.className += " invalid";
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');


		} else if (!validateEmail(input.value)) {
			input.parentElement.className += " invalid";
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid email id</div>');



		} else {
			input.parentElement.classList.remove("invalid");

		}
	}  else if (input.type === 'select-one') {
		var mainParent = input.parentElement;
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		var selectedOption = input.value;
		// Assuming `input` is a <select> element
		var selectedOptions = Array.from(input.selectedOptions);
	
		if (!selectedOption || selectedOption.trim() === '') {
			mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
			input.parentElement.classList.add("invalid");
			
		} else {
			input.parentElement.classList.remove("invalid");
			if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-message')) {
				input.nextElementSibling.remove();
			}
			
		}
	} else if (input.type === 'select-multiple') {
		var mainParent = input.parentElement;
		var clonedInput = mainParent.querySelector('.choices__input--cloned');
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
	
		// Assuming `input` is a <select> element
		var selectedOptions = Array.from(input.selectedOptions);
	
		if (selectedOptions.length === 0) {
			mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
			input.parentElement.classList.add("invalid");
			clonedInput.style.display = 'block';
		} else {
			input.parentElement.classList.remove("invalid");
			if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-message')) {
				input.nextElementSibling.remove();
			}
			clonedInput.style.display = 'none';
		}
	} else if (input.type === 'checkbox') {
		var inputID = document.getElementById(input.id).getAttribute('data-id');
		var checkboxes = document.querySelectorAll('input[name="' + inputID + '"]');
		var zippyCheckBox = document.querySelector(".zippyCheckBoxes");
		var maxSelection = parseInt(zippyCheckBox.dataset.maxSelectedOptions) || 2;
		var inputLabel = zippyCheckBox.dataset.label;
		var errorMessageInserted = zippyCheckBox.querySelector('.error-message') !== null;

		// Check the number of checked checkboxes
		var checkedCount = 0;
		checkboxes.forEach(function(checkbox) {
			if (checkbox.checked) {
				checkedCount++;
			}
		});

		if (checkedCount === 0 && !errorMessageInserted) {
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select at least one option for ' + inputLabel + '</div>');

			valid = false;
		} else if (checkedCount > maxSelection && !errorMessageInserted) {
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Select up to ' + maxSelection + ' options for ' + inputLabel + '</div>');

			valid = false;
		} else {
			var parent = input.parentElement;
			if (parent && parent.nextElementSibling) {
				
				parent.nextElementSibling.remove()
			}
			input.parentElement.classList.remove("invalid");

		}
	} else if (input.type === 'radio') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		var inputID = document.getElementById(input.id).getAttribute('data-id');
		var radioButtons = document.getElementById('step' + currentStep).querySelectorAll('input[name="' + inputID + '"]');

		var checked = false;
		for (var k = 0; k < radioButtons.length; k++) {
			if (radioButtons[k].checked) {
				checked = true;
				break;
			}
		}

		if (!checked) {
			// input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select ' + inputLabel + '</div>');



		} else {
			var parent = input.parentElement;
			if (parent && parent.nextElementSibling) {
				parent.nextElementSibling.remove()
			}
			input.parentElement.classList.remove("invalid");

		}
	} else if (input.type === 'url') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		if (input.value === '') {
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');



		} else if (!isValidHttpUrl(input.value)) {
			input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid url</div>');



		} else {
			input.parentElement.classList.remove("invalid");

		}
	} else if (input.type === 'file') {
		var inputLabel = document.getElementById(input.id).getAttribute('data-label');
		if (input.files.length === 0) {
			// input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select a file</div>');


		} else {



			const maxFileSize = input.dataset.max * 1024 * 1024; // 2MB in bytes
			const selectedFile = input.files[0];

			// Check file type based on extension
			const allowedFileTypes = document.getElementById(input.id).getAttribute('data-extention'); // Add or modify the allowed file types
			const cleanedFileTypes = allowedFileTypes.replace(/\./g, '');
			const fileName = selectedFile.name;
			const fileExtension = fileName.split('.').pop().toLowerCase();

			if (!allowedFileTypes.includes(fileExtension)) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Invalid file type. Allowed types are: ' + cleanedFileTypes + '</div>');
				valid = false;
			} else if (selectedFile.size > maxFileSize) {
				input.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">File size exceeds ' + input.dataset.max + 'MB limit</div>');

			} else {
				input.parentElement.classList.remove("invalid");
			}
		}
	}




}

function showStep(stepNumber, formid,zippyCode) {
    

    // Get the form element
    var formElement = document.querySelector(`.${zippyCode}`);

    if (formElement) {
        // Hide all tabs within the form
        formElement.querySelectorAll('.tab').forEach(step => {
            step.style.display = 'none';
        });

        // Show the specific step within the form
        var stepToShow = formElement.querySelector(`#step${stepNumber}`);
        if (stepToShow) {
            stepToShow.style.display = 'block';
            fixStepIndicator(stepNumber,formid,zippyCode);
        } else {
            console.error(`Step element with ID 'step${stepNumber}' not found in form${formid}`);
        }
    } else {
        console.error(`Form element with class 'form${formid}' not found.`);
    }
}

function nextStep(button) {

	var validd = false;
	var currentStepNo = parseInt(button.getAttribute('data-step-no'),10);
    var formid =  button.getAttribute('data-form-id');
    var stepid = button.getAttribute('data-step-id');
	var zippyCode = button.getAttribute('data-zippy-code');
    
    // Use document.querySelector to get the first element with the specified class
    const formElement = document.querySelector('form.'+zippyCode+' .step'+stepid+'');
	if (formElement) {
        // Use querySelectorAll on the found element
        const inputs = formElement.querySelectorAll('input, select, select[multiple], textarea');
       
        // Assuming 'valid' is a variable that is defined somewhere
        
		validd = validateForm(currentStepNo, stepid, formid,zippyCode);
        if (validd) {
            sendDataToEndpoint(currentStepNo, inputs, formid, stepid,zippyCode);
            // Redirect to a success page or show a success message
        } else {
            // Show error messages if necessary
        }
    } else {
        console.error(`Form element with class 'form${formid}' not found.`);
    }
}

// function previousStep(stepNumber) {
// 	var i, x = document.getElementsByClassName("step");
// 	n = stepNumber - 1;
// 	for (i = 0; i < x.length; i++) {
// 		x[n].className = x[n].className.replace(" finish", "");

// 	}
// 	showStep(stepNumber);
// }

function previousStep(button) {
    var currentStepNo = parseInt(button.getAttribute('data-step-no'), 10);
    var formid = button.getAttribute('data-form-id');
    var stepid = button.getAttribute('data-step-id');
	var zippyCode = button.getAttribute('data-zippy-code');
    const formElements = document.querySelector(`.${zippyCode}`);
    var i, x = formElements.querySelectorAll(".step");
    n = currentStepNo - 1;

    for (i = 1; i < x.length; i++) {
        if (i === n) {
var k = i-1;
            // Replace "finish" class with "your-new-class" for the second step
            x[k].classList.remove("finish");
            x[k].classList.add("your-new-class");
        }
    }

    showStep(n, formid,zippyCode);
}


function validateForm(currentStep,stepid,formid,zippyCode) {
	// This function deals with validation of the form fields
	// console.log('validate', currentStep);
	var cStep = currentStep;
	var j = cStep - 1;
	var x, y, i;
	var valid = true;
	
	x = document.querySelector('form.'+zippyCode+' .tab[data-step-id="' + stepid + '"]');
	y = x.getElementsByClassName("require");
	var errorDivs = document.querySelectorAll(".error-message");
	errorDivs.forEach(function(errorDiv) {
		errorDiv.remove();
	});
	var radioErrors = {};
	// A loop that checks every input field in the current tab:
	for (i = 0; i < y.length; i++) {
		var errorSpan = document.getElementsByClassName('error-message' + i);
		if (y[i].type === 'text' || y[i].type === 'tel' || y[i].type === 'textarea' || y[i].type === 'date' || y[i].type === 'time') {
			var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
			if (y[i].classList.contains('zippy-decimal')) {
				if (y[i].value === '') {

					y[i].parentElement.classList.add("invalid");
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;
				} else if (
					isNaN(y[i].value) || // Check if it's not a valid number
					parseFloat(y[i].value) < parseFloat(y[i].min) ||
					parseFloat(y[i].value) > parseFloat(y[i].max)
				) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' value should be between ' + y[i].min + ' and ' + y[i].max + '.</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;
				} else {
					y[i].parentElement.classList.remove('invalid');
				}
			} else {
				if (y[i].value.trim() === '') {
					y[i].parentElement.className += " invalid";
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;

				} else if (y[i].value.length < y[i].minLength || y[i].length > y[i].maxLength) {

					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' must contain atleast ' + y[i].minLength + '  characters.</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;

				} else {
					y[i].parentElement.classList.remove("invalid");

				}
			}
		} else if (y[i].type === 'number') {
			var minValue = y[i].min;
			var maxValue = y[i].max;
			const enteredValue = parseFloat(y[i].value); // Convert input value to a number
			var decimal = document.getElementById(y[i].id).getAttribute('data-decimal');
			var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
			if (decimal > 0) {

				if (y[i].value === '') {

					y[i].parentElement.classList.add("invalid");
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;
				} else if (isNaN(enteredValue) || enteredValue < y[i].min || enteredValue > y[i].max) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + y[i].min + ' and ' + y[i].max + '.</div>');
					valid = false;
				} else if (!isValidDecimal(enteredValue, decimal, minValue, maxValue)) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a number with a maximum of ' + decimal + ' decimal places.</div>');
					valid = false;
				} else {
					y[i].parentElement.classList.remove("invalid");
				}
			} else {
				if (y[i].value === '') {

					y[i].parentElement.classList.add("invalid");
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;
				} else if (isNaN(enteredValue) || enteredValue < y[i].min || enteredValue > y[i].max) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + y[i].min + ' and ' + y[i].max + '.</div>');
					valid = false;
				} else {
					y[i].parentElement.classList.remove("invalid");
				}
			}
		} else if (y[i].type === 'email') {
			if (y[i].value === '') {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				y[i].parentElement.className += " invalid";
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else if (!validateEmail(y[i].value)) {
				y[i].parentElement.className += " invalid";
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid email id</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else {
				y[i].parentElement.classList.remove("invalid");

			}
		} else if (y[i].type === 'select-one') {
			var selectedOption = y[i].value;
			if (!selectedOption || selectedOption.trim() === '') {
				var mainParent = y[i].parentElement;
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
				y[i].parentElement.className += " invalid";
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else {
				y[i].parentElement.classList.remove("invalid");

			}
		} else if (y[i].type === 'select-multiple') {
			if (y[i].value === '') {
				var mainParent = y[i].parentElement;
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				mainParent.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select an option from ' + inputLabel + '</div>');
				y[i].parentElement.className += " invalid";
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else {
				y[i].parentElement.classList.remove("invalid");

			}
		} else if (y[i].type === 'radio') {
			var inputLabel = y[i].getAttribute('data-label');
			var inputID = y[i].getAttribute('data-id');
			var radioButtons = document.querySelectorAll('input[name="' + inputID + '"]');
		
			var checked = Array.from(radioButtons).some(function(button) {
				return button.checked;
			});
		
			if (!checked && !radioErrors[inputID]) {
				var errorMessage = document.createElement('div');
				errorMessage.className = 'error-message';
				errorMessage.textContent = 'Please select an option ' + inputLabel;
		
				y[i].parentElement.insertAdjacentElement('afterend', errorMessage);
				y[i].scrollIntoView({ behavior: 'smooth' });
		
				valid = false;
				radioErrors[inputID] = true; // Mark this radio group as having an error message
			} 
		} else if (y[i].type === 'checkbox') {
			var inputID = document.getElementById(y[i].id).getAttribute('data-id');
			var checkboxes = document.querySelectorAll('input[name="' + inputID + '"]');
			var zippyCheckBox = document.querySelector(".zippyCheckBoxes");
			var zippyCheckBoxError = document.querySelector(".zippyCheckBoxes-option");
			var maxSelection = parseInt(zippyCheckBox.dataset.maxSelectedOptions) || 2;
			var inputLabel = zippyCheckBox.dataset.label;
			var errorMessageInserted = zippyCheckBoxError.querySelector('.error-message') !== null;

			// Check the number of checked checkboxes
			var checkedCount = 0;
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					checkedCount++;
				}
			});

			if (checkedCount === 0 && !errorMessageInserted) {
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please select at least one option for ' + inputLabel + '</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			} else if (checkedCount > maxSelection && !errorMessageInserted) {
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Select up to ' + maxSelection + ' options for ' + inputLabel + '</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			}
		} else if (y[i].type === 'url') {
			if (y[i].value === '') {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' required field</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else if (!isValidHttpUrl(y[i].value)) {
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid url</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;

			} else {
				y[i].parentElement.classList.remove("invalid");

			}
		} else if (y[i].type === 'file') {
			if (y[i].files.length === 0) {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' is required</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			} else {
				const maxFileSize = y[i].dataset.max * 1024 * 1024; // 2MB in bytes
				const selectedFile = y[i].files[0];

				// Check file type based on extension
				const allowedFileTypes = document.getElementById(y[i].id).getAttribute('data-extention'); // Add or modify the allowed file types
				const cleanedFileTypes = allowedFileTypes.replace(/\./g, '');
				const fileName = selectedFile.name;
				const fileExtension = fileName.split('.').pop().toLowerCase();

				if (!allowedFileTypes.includes(fileExtension)) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Invalid file type. Allowed types are: ' + cleanedFileTypes + '</div>');
					valid = false;
				} else if (selectedFile.size > maxFileSize) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">File size exceeds ' + y[i].dataset.max + 'MB limit</div>');
					valid = false;
				} else {
					y[i].parentElement.classList.remove("invalid");
				}
			}
		}
	}

	// If the valid status is true, mark the step as finished and valid:
	var finalStep = document.getElementById(`totalStep${formid}`).value;
	if (valid) {
		var validAll = validateAllFields(cStep,stepid,zippyCode);
		valid = validAll;
		if(validAll){
		if (finalStep == 1) {
			return valid;
		} else {
			var regForm = document.getElementsByClassName(`form${formid}`);

// Check if regForm has elements (length greater than 0)
		if (regForm.length > 0) {
			// Access the first element in the collection or loop through all elements if needed
			var steps = regForm[0].querySelectorAll(".step");

			if (steps[j].classList.contains('finish')) {
				// do some stuff
			} else {
				steps[j].classList.add("finish");
			}
		}
		}
	}
	return valid;
	}

	 // return the valid status

}


function validateAllFields(currentStep,stepid,zippyCode) {
	// This function deals with validation of the form fields
	// console.log('validate', currentStep);
	var valid = true;
	var cStep = currentStep;
	var j = cStep - 1;
	var x, y, i;
	x = document.querySelector('form.'+zippyCode+' .tab[data-step-id="' + stepid + '"]');
	y = x.querySelectorAll('input, select');
	// y = x[j].querySelectorAll('input, select');
	var errorDivs = document.querySelectorAll(".error-message");
	errorDivs.forEach(function(errorDiv) {
		errorDiv.remove();
	});
	var radioErrors = {};
	// A loop that checks every input field in the current tab:
	for (i = 0; i < y.length; i++) {
		var errorSpan = document.getElementsByClassName('error-message' + i);
		if (y[i].type === 'text' || y[i].type === 'tel' || y[i].type === 'textarea' || y[i].type === 'date' || y[i].type === 'time') {

			if (y[i].classList.contains('zippy-decimal')) {
				if (y[i].value != '') {
					var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
					if (isNaN(y[i].value) || parseFloat(y[i].value) < parseFloat(y[i].min) || parseFloat(y[i].value) > parseFloat(y[i].max)) {
						y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' value should be between ' + y[i].min + ' and ' + y[i].max + '.</div>');
						y[i].scrollIntoView({
							behavior: 'smooth'
						});
						valid = false;
					} else {
						y[i].parentElement.classList.remove('invalid');
					}
				}
			} else {
				if (y[i].value.length > 0) {

					var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
					if (y[i].value.length < y[i].minLength || y[i].length > y[i].maxLength) {

						y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + inputLabel + ' length should be between ' + y[i].minLength + ' and ' + y[i].maxLength + ' characters.</div>');
						y[i].scrollIntoView({
							behavior: 'smooth'
						});
						valid = false;

					}
				}
			}
		} else if (y[i].type === 'checkbox') {
			var inputID = document.getElementById(y[i].id).getAttribute('data-id');
			var checkboxes = document.querySelectorAll('input[name="' + inputID + '"]');
			var zippyCheckBox = document.querySelector('.'+zippyCode+' .zippyCheckBoxes');
			var zippyCheckBoxError = document.querySelector('.'+zippyCode+' .zippyCheckBoxes-option');
			var maxSelection = parseInt(zippyCheckBox.dataset.maxSelectedOptions) || 2;
			var inputLabel = zippyCheckBox.dataset.label;
			var errorMessageInserted = zippyCheckBoxError.querySelector('.error-message') !== null;

			// Check the number of checked checkboxes
			var checkedCount = 0;
			checkboxes.forEach(function(checkbox) {
				if (checkbox.checked) {
					checkedCount++;
				}
			});

			 if (checkedCount > maxSelection && !errorMessageInserted) {
				y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Select up to ' + maxSelection + ' options for ' + inputLabel + '</div>');
				y[i].scrollIntoView({
					behavior: 'smooth'
				});
				valid = false;
			}
		} else if (y[i].type === 'number') {
			var minValue = y[i].min;
			var maxValue = y[i].max;
			const enteredValue = parseFloat(y[i].value); // Convert input value to a number
			var decimal = document.getElementById(y[i].id).getAttribute('data-decimal');
			var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
			if (decimal > 0) {

				if (y[i].value > 0) {


					if (isNaN(enteredValue) || enteredValue < y[i].min || enteredValue > y[i].max) {
						y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + y[i].min + ' and ' + y[i].max + '.</div>');
						valid = false;
					} else if (!isValidDecimal(enteredValue, decimal, minValue, maxValue)) {
						y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a number with a maximum of ' + decimal + ' decimal places.</div>');
						valid = false;
					} else {
						y[i].parentElement.classList.remove("invalid");
					}
				}
			} else {
				if (y[i].value > 0) {


					if (isNaN(enteredValue) || enteredValue < y[i].min || enteredValue > y[i].max) {
						y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Enter a valid number between ' + y[i].min + ' and ' + y[i].max + '.</div>');
						valid = false;
					} else {
						y[i].parentElement.classList.remove("invalid");
					}
				}
			}
		} else if (y[i].type === 'email') {
			if (y[i].value != '') {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');

				if (!validateEmail(y[i].value)) {
					y[i].parentElement.className += " invalid";
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid email id</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;

				} else {
					y[i].parentElement.classList.remove("invalid");

				}
			}
		} else if (y[i].type === 'url') {
			if (y[i].value != '') {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');


				if (!isValidHttpUrl(y[i].value)) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Please enter valid url</div>');
					y[i].scrollIntoView({
						behavior: 'smooth'
					});
					valid = false;

				} else {
					y[i].parentElement.classList.remove("invalid");

				}
			}
		} else if (y[i].type === 'file') {
			if (y[i].files.length != 0) {
				var inputLabel = document.getElementById(y[i].id).getAttribute('data-label');


				const maxFileSize = y[i].dataset.max * 1024 * 1024; // 2MB in bytes
				const selectedFile = y[i].files[0];

				// Check file type based on extension
				const allowedFileTypes = document.getElementById(y[i].id).getAttribute('data-extention'); // Add or modify the allowed file types
				const cleanedFileTypes = allowedFileTypes.replace(/\./g, '');
				const fileName = selectedFile.name;
				const fileExtension = fileName.split('.').pop().toLowerCase();

				if (!allowedFileTypes.includes(fileExtension)) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">Invalid file type. Allowed types are: ' + cleanedFileTypes + '</div>');
					valid = false;
				} else if (selectedFile.size > maxFileSize) {
					y[i].parentElement.insertAdjacentHTML('afterend', '<div class="error-message">File size exceeds ' + y[i].dataset.max + 'MB limit</div>');
					valid = false;
				} else {
					y[i].parentElement.classList.remove("invalid");
				}
			}
		}
	}


	return valid; // return the valid status

}

function validateEmail(email) {
	// Basic email validation regex pattern
	var pattern = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
	return pattern.test(email);
}

function isValidHttpUrl(string) {
	var re = /^((https?|ftp|smtp):\/\/)?(www.)?[a-z0-9]+(\.[a-z]{2,}){1,3}(#?\/?[a-zA-Z0-9#]+)*\/?(\?[a-zA-Z0-9-_]+=[a-zA-Z0-9-%]+&?)?$/;
	return re.test(string);
};

function isValidDecimal(value, decimalPlaces, minValue, maxValue) {
	// Check if it's a valid number with the desired decimal places
	const decimalPattern = new RegExp(`^\\d+(\\.\\d{1,${decimalPlaces}})?$`);
	if (!decimalPattern.test(value.toString())) {
		return false;
	}

	// Check if it's within the specified min and max values
	return value >= minValue && value <= maxValue;
}

async function sendDataToEndpoint(currentStepNo, inputs, formid, stepid,zippyCode) {
    document.getElementById('nextBtn').disabled = true;
	var formApiUrl = zippyApiUrl.optionValue;
	var submitId = document.getElementById(`sumission_id${formid}`).value;
	if (submitId == '') {
		var url = formApiUrl + `dynamic-form/${formid}/submit/${stepid}`;

	} else {
		var url = formApiUrl + `dynamic-form/${formid}/submit/${stepid}/${submitId}`;
	}
	const stepNumber = currentStepNo;
	const stepno = stepNumber + 1;
	const formData = new FormData();
	const appendedRadioNames = new Set();
	const appendedCheckBoxes = new Set();
	const captchaElement = document.querySelector('form.'+zippyCode+' .step'+stepid+'');
	const reCaptchaDiv = captchaElement.querySelector('#' + zippyCode + '-grecaptcha');
    if (reCaptchaDiv) {
        const siteKey = reCaptchaDiv.getAttribute('data-site-key');

        const token = await grecaptcha.execute(siteKey);
        formData.append('google_recaptcha_token', token);
    }else{
		
	}
	
	inputs.forEach(input => {
		if (input.type == 'select-one' || input.type == 'select-multiple') {
			const options = document.getElementById(input.id).selectedOptions;
			const optionValues = Array.from(options).map(option => option.value).filter(value => value.trim() !== '');
			if (optionValues.length > 0) {
				formData.append(input.id, JSON.stringify(optionValues));
			} else {
				formData.append(input.id, JSON.stringify([]));
			}

		} else if (input.type === 'checkbox') {
			if (!appendedCheckBoxes.has(input.name)) {
				const checkboxes = document.querySelectorAll('input[name="' + input.name + '"]:checked');
				const checkboxValues = Array.from(checkboxes).map(checkbox => checkbox.value);

				if (checkboxValues.length > 0) {
					formData.append(input.name, JSON.stringify(checkboxValues));
					appendedCheckBoxes.add(input.name);
				} else {
					formData.append(input.name, JSON.stringify([])); // or you can skip appending if you want to ignore it
					appendedCheckBoxes.add(input.name);
				}
			}
		} else if (input.type == 'date') {
			const dateValue = input.value;
			const dateParts = dateValue.split('-');
			if (dateParts.length === 3) {
				const formattedDate = `${dateParts[1]}-${dateParts[2]}-${dateParts[0]}`;
				formData.append(input.id, formattedDate);
			}

		} else if (input.type === 'time') {
			const inputValue = input.value;
			if (inputValue.trim() !== '') {
				const [hours, minutes] = inputValue.split(':');
				const ampm = hours >= 12 ? 'PM' : 'AM';
				const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
				const formattedTime = `${formattedHours}:${minutes} ${ampm}`;
				formData.append(input.id, formattedTime);
			} else {
				formData.append(input.id, '');
			}

		} else if (input.type === 'radio') {
			if (!appendedRadioNames.has(input.name)) {
				const radioButtons = document.getElementsByName(input.name);
				const selectedRadioButton = Array.from(radioButtons).find(radio => radio.checked);
			if (selectedRadioButton) {
					formData.append(input.name, selectedRadioButton.value);
					appendedRadioNames.add(input.name);
				} else {
					formData.append(input.name, '');
					appendedRadioNames.add(input.name);
				}
			}
		} else if (input.type === 'file') {
			const fileInput = document.getElementById(input.id);
			if (fileInput.files.length > 0) {
				formData.append(input.id, fileInput.files[0]);
			} else {

				formData.append(input.id, '');
			}

		} else {

			if (input.id === '') {

			} else {
				formData.append(input.id, input.value);

			}
		}
	});


	const response = await fetch(url, {
		method: 'POST',
		body: formData,
	});

	const result = await response.json();

    
	//return result;


	var finalStep = document.getElementById(`totalStep${formid}`).value;
	if (response.status == 200) {
		document.getElementById('nextBtn').disabled = false;
		const fromTypeValue = result.data.from_type;
		if (finalStep == currentStepNo) {
			if( fromTypeValue == 'payment_form'){
				// Extract values from the API response
				const clientSecret = result.data.client_secret;
				const publicKey = result.data.public_key;
				const accountId = result.data.connected_account_id;

				// Initialize Stripe with the retrieved values
				const stripe = Stripe(publicKey, {
				stripeAccount: accountId,
				});

				initialize();

				// Create a Checkout Session as soon as the page loads
				async function initialize() {
				const checkout = await stripe.initEmbeddedCheckout({
					clientSecret,
				});
				var y = document.querySelector('.'+zippyCode+'');
				document.querySelector('.'+zippyCode+'').style.display = 'none';
				// Mount Checkout
				const checkoutElement = document.getElementById('zippy-checkout'+zippyCode+'');
				checkoutElement.innerHTML = '';
				checkout.mount('#zippy-checkout'+zippyCode+'');
			}
			} else {
			var y = document.querySelector(`.${zippyCode}`);
			document.querySelector(`.${zippyCode}`).style.display = 'none';
			var successMsg = document.querySelectorAll(`.zippySuccesMsg${zippyCode}`);
			successMsg .forEach(function(successMsgs ) {
				successMsgs.remove();
			});
			y.reset();
			var formsubmitId =  result.data.submission_id;
			var ajaxUrl = my_script_vars.ajax_url;

			// Trigger the AJAX request
			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'zippy_ajax_action',
					form_id: formid,
					formsubmitId : formsubmitId
				},
				success: function(response) {
					y.insertAdjacentHTML('afterend', '<h2 class="zippySuccesMsg'+zippyCode+'" style="text-align:center">' + result.msg + '</h2>');

					if (response.status === 'refresh') {
						setTimeout(function() {
							location.reload();
						}, 1500);
					} else if (response.status === 'redirect') {
						setTimeout(function() {
							window.location.href = response.url;
						}, 1500);
					}
				},
				error: function(error) {
					console.error('Error triggering action hook:', error);
				}
			});

			}
		} else {
			showStep(stepno,formid,zippyCode);
			document.querySelector(`.${zippyCode}`).scrollIntoView({
				behavior: 'smooth'
			});
			var submitIdInput = document.getElementById(`sumission_id${formid}`);
			submitIdInput.value = result.data.submission_id;
		}
	}
	if (response.status == 400) {
		document.getElementById('nextBtn').disabled = false;
		var errorDivs = document.querySelectorAll(".error-message");
		errorDivs.forEach(function(errorDiv) {
			errorDiv.remove();
		});
		var fieldForm = document.querySelector(`.${zippyCode} `);
		// Loop through the "data" object in the response
		for (const key in result.data) {
			if (result.data.hasOwnProperty(key)) {
				const errorMessage = result.data[key];
				const inputField = fieldForm.querySelector(`[id="${key}"]`); // Assuming the key is the same as the input element's ID
				if (inputField) {
					// Set the error message as the input field's value
					inputField.parentElement.insertAdjacentHTML('afterend', '<div class="error-message">' + errorMessage + '</div>');
				}
			}
		}
		if (result.msg) {
			// Create a new div element for the error message
			var errorStep = document.querySelector('form.'+zippyCode+' .step'+stepid+'');
			var lastElement = errorStep.lastElementChild;
			var errorMessageDiv = document.createElement('div');
			errorMessageDiv.classList.add('error-message');
			errorMessageDiv.textContent = result.msg;
			errorMessageDiv.style.marginTop = '1rem'; // Set margin-top inline
    		errorMessageDiv.style.textAlign = 'center';
			errorStep.insertBefore(errorMessageDiv, lastElement);
		}

	}

}

function submitForm(button) {
    var currentStepNo = parseInt(button.getAttribute('data-step-no'),10);
    var formid =  button.getAttribute('data-form-id');
    var stepid = button.getAttribute('data-step-id');
    var zippyCode = button.getAttribute('data-zippy-code');
    // Use document.querySelector to get the first element with the specified class
    const formElement = document.querySelector(`form.`+zippyCode+` .step${stepid}`);
    
    // Check if the element is found before proceeding
    if (formElement) {
        // Use querySelectorAll on the found element
		
        const inputs = formElement.querySelectorAll('input, select, select[multiple], textarea');
        
        // Assuming 'valid' is a variable that is defined somewhere
        var valid = validateForm(currentStepNo, stepid, formid,zippyCode);
        if (valid) {
            sendDataToEndpoint(currentStepNo, inputs, formid, stepid,zippyCode);
            // Redirect to a success page or show a success message
        } else {
            // Show error messages if necessary
        }
    } else {
        console.error(`Form element with class 'form${formid}' not found.`);
    }
}

function fixStepIndicator(stepNumber, formid,zippyCode) {
    // Get the form element
    var formElement = document.querySelector(`.${zippyCode}`);

    if (formElement) {
        // Get all step elements within the form
        var x = formElement.querySelectorAll(".step");

        var progress = formElement.querySelector("#progress-steps");
        var k = 100 / (x.length - 1);
        var n = stepNumber - 1;

        for (var i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
            
            // Check screen width and set style accordingly
            if (window.innerWidth <= 767) {
                progress.style.height = k * n + "%";
            } else {
                progress.style.width = k * n + "%";
            }
        }

        // Add the "active" class to the current step within the form
        x[n].className += " active";
    } else {
        console.error(`Form element with class 'form${formid}' not found.`);
    }
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

function formatNumber(input, decimalPlaces) {
	if (input.value !== '') {
		input.value = parseFloat(input.value).toFixed(decimalPlaces);
	}
}


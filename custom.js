(function($) {
        $(document).ready(function() {
            
			
			    // Initialize international telephone input for Gc-telephone-number
    var input1 = document.querySelector("#Gc-telephone-number");
    var iti1 = window.intlTelInput(input1, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });
    
    // Fill the value to form field with country code and plus sign on focus out for Gc-telephone-number
    $('#Gc-telephone-number').on('blur', function(){
        // Clear previous country code and plus sign
        var inputValue = $(this).val();
        inputValue = inputValue.replace(/\+\d*/g, '');
        $(this).val(inputValue);
        
        // Append new country code and plus sign
        var dialCode = iti1.getSelectedCountryData().dialCode;
        var phoneNumber = '+' + dialCode + $(this).val();
        $(this).val(phoneNumber);
    });

    // Format input as phone number for Gc-telephone-number
    $('#Gc-telephone-number').on('input', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        $(this).val('(' + x[1] + ') ' + x[2] + (x[2] ? '-' : '') + x[3]);
    });

    // Initialize international telephone input for business_telephone
    var input2 = document.querySelector("#business_telephone");
    var iti2 = window.intlTelInput(input2, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });
    
    // Fill the value to form field with country code and plus sign on focus out for business_telephone
    $('#business_telephone').on('blur', function(){
        // Clear previous country code and plus sign
        var inputValue = $(this).val();
        inputValue = inputValue.replace(/\+\d*/g, '');
        $(this).val(inputValue);
        
        // Append new country code and plus sign
        var dialCode = iti2.getSelectedCountryData().dialCode;
        var phoneNumber = '+' + dialCode + $(this).val();
        $(this).val(phoneNumber);
    });

    // Format input as phone number for business_telephone
    $('#business_telephone').on('input', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        $(this).val('(' + x[1] + ') ' + x[2] + (x[2] ? '-' : '') + x[3]);
    });


            // Handle change event on industry dropdown
				jQuery(document).on("change", "#gc-industry", function() {
					var gc_subcategory = jQuery("#gc-subcategory");
					var subcategorySelect = jQuery("#subcategory");
                    subcategorySelect.empty(); // Clear previous options
					
					// Check if any elements with the .mult-select-tag class exist
					if (jQuery('.mult-select-tag').length > 0) {
						// Hide the elements with the .mult-select-tag class
						gc_subcategory.css('display', 'none');
						jQuery('.mult-select-tag').css('display', 'none');
					} else {
						// Handle the case where no elements with the .mult-select-tag class exist
						console.log(".mult-select-tag class not found");
					}
    var cat_id = jQuery(this).val().trim();
    if (cat_id !== '') {
        jQuery.ajax({
            url: blog.ajaxurl,
            type: 'post',
            data: {
                action: 'gc_get_sub_category_name',
                cat_id: cat_id,
            },
            success: function(response) {
                try {
                    response = JSON.parse(response); // Parse the JSON response
					if (response.error) {
								// Handle the error
								console.log(response.error);
						} 
				else {
                    //var subcategorySelect = jQuery("#subcategory");
                    //subcategorySelect.empty(); // Clear previous options
					gc_subcategory.css('display', 'block');
                    // Iterate over each object in the response data
                    jQuery.each(response, function(index, item) {
                        // Append options for each subcategory
                        subcategorySelect.append("<option value='" + item.id + "'>" + item.name + "</option>");
                    });

                    // Initialize MultiSelectTag
                    new MultiSelectTag('subcategory', {
                        rounded: true,
                        shadow: true,
                        placeholder: 'Search',
                        tagColor: {
                            textColor: '#327b2c',
                            borderColor: '#92e681',
                            bgColor: '#eaffe6',
                        },
                        onChange: function(values) {
                            console.log(values);
                        }
                    });
				}
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error(error);
            }
        });
    }
});
				// Handle change event on country dropdown
				jQuery(document).on("change","#Gc-businessCountry",function() {
				// Cache the state dropdown element
				var $stateDropdown = $('#Gc-businessState');
				var $stateField = $('#state_field');
                var selectedCountry = $(this).val();
                var countryStates = blog.woocommerce_states;

                // Clear existing options in state dropdown
                $stateDropdown.empty();

					$("#Gc-businessState").show();
                    $(".Cg_dynamic").remove();


                // Add default option
                $stateDropdown.append($('<option>', {
                    value: '',
                    text: 'Select a state'
                }));

                // Add states for the selected country
                if (countryStates[selectedCountry]) {
                    $.each(countryStates[selectedCountry], function(code, name) {
                        $stateDropdown.append($('<option>', {
                            value: code,
                            text: name
                        }));
                    });
                    // Hide text field
                    $stateField.show();
                } else {
                    // If no states, show text field instead and hide dropdown
                    $("#Gc-businessState").hide();
                    $("#Gc-businessState").after('<input type="text" id="Gc-businessState" class="Cg_dynamic" name="businessState" placeholder="Business City/Country/Island">');
                }
            });
        });
    })(jQuery);

jQuery(document).ready(function(){
	
	  // Function to check password match
    function checkPasswordMatch() {
        var password = jQuery('#gc_password').val();
        var confirm_password = jQuery('#confirm_password').val();
        if (password != '' && confirm_password != '') {
            if (password == confirm_password) {
                jQuery('#gc-message').html('Matching').css('color', 'green');
                jQuery('#step1_submit').prop('disabled', false); // Enable submit button
            } else {
                jQuery('#gc-message').html('Not Matching').css('color', 'red');
                jQuery('#step1_submit').prop('disabled', true); // Disable submit button
            }
        } else {
            jQuery('#gc-message').html('');
            jQuery('#step1_submit').prop('disabled', false); // Enable submit button if fields are empty
        }
    }

    // Check password match on keyup in confirm password field only
    jQuery('#confirm_password').on('keyup', checkPasswordMatch);

    // Clear message when clicking on other form fields
    /* jQuery('#gc-step1Form input[type="text"], #gc-step1Form input[type="number"]').not('#password, #confirm_password').on('click', function() {
        jQuery('#gc-message').html('');
    }); */
	
	jQuery(document).on("click","#business_telephone",function() {
		 jQuery('#gc-message').html('');
	});
		
	jQuery(document).on("focusout","#gc_store_name",function() {
		
		var self = jQuery(this);
		if (self.val().trim() !== '') {
			jQuery.ajax({
            url: blog.ajaxurl,
            type: 'post',
            data: {
                action: 'gc_get_stor_name',
                url_slug: self.val(),
                vendor_id: 0,
            },
            success: function(response) {
				if ( response.success === true ) {
					if(response.data.url===false)
					{
						jQuery('#step1_submit').prop('disabled', true); 
						jQuery('#url-alart-mgs').removeClass('text-success').addClass('text-danger').text(response.data.message);
					}
					else{
                         jQuery('#url-alart-mgs').removeClass('text-danger').addClass('text-success').text(response.data.message);
						 jQuery('#gc-url-alart').text(response.data.url);
                        //jQuery('#url-alart-mgs').removeClass('text-danger').addClass('text-success').//text(dokan_user_profile.seller.available); 
						console.log('yes');
					}
                    }
					else{
						jQuery('#url-alart-mgs').removeClass('text-success').addClass('text-danger').text(response.data.message);
                        //$('#url-alart-mgs').removeClass('text-success').addClass('text-danger')
					}
				
            }
        });
		}
		else{
			jQuery('#url-alart-mgs').text('');
			console.log('empty');
		}
		
	});
	jQuery(document).on("click",".Gc_add_btn",function() {
        var quantity = jQuery('.Gc-quantity').val();
        var product_id = jQuery(this).attr("data-product-id");
		
       
		jQuery.ajax({
            url: blog.ajaxurl,
            type: 'post',
            data: {
                action: 'gc_custome_add_to_cart',
                product_id: product_id,
                quantity: quantity
            },
            success: function(response) {
				//let reg_response = JSON.parse(response);
				
                if(response.status==true){
					console.log(response);
					//console.log('here');
					window.location.href = 'abc.com';
					//jQuery('#Gc_responce').html(reg_response.message);
                    
                }
				else{
					console.log('not added');
				}
                // You can customize the success message or redirect to the cart page here
            }
        });
    });
	
	
		 
    
	
	/* jitendra */
	jQuery("#image_data").hide(); 
	jQuery("#showdiv").hide(); 

	jQuery('#file_numbers').on('change', function() {
		document.getElementById("loader_image").style.display = "block";
        file_data = jQuery(this).prop('files')[0];
		form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('action', 'file_upload_callback_glarry6');
        form_data.append('security', blog.security);
		jQuery.ajax({
            url: blog.ajaxurl,
            type: 'POST',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
				var data = jQuery.parseJSON(response);
				if(data.message){
					document.getElementById("error_message").innerHTML = data.message;
					document.getElementById("loader_image").style.display = "none";
					jQuery("#showdiv_first").show();
					jQuery("#showdiv").show();
					jQuery("#showdiv_second").show();  
				}else if (data.notice) {
					document.getElementById("error_message").innerHTML = '';
					document.getElementById("notice_message").innerHTML = data.notice;
					document.getElementById("loader_image").style.display = "none";
					jQuery("#targetDiv, #showdiv, #showdiv_first, #showdiv_mid,#nextButton,#showdiv_second").show();
				}else{
					document.getElementById("error_message").innerHTML = '';
					document.getElementById("loader_image").style.display = "none";
              		var TIN = data.TIN;
              		var Licence_Number = data.Licence_Number;
              		var Expires = data.Expires;
              		var Trading_As = data.Trading_As; 
              		var Located_At = data.Located_At;
              		var Business_Type = data.Business_Type;
              		var Valid_Period = data.Valid_start;		
              		var Valid_end = data.Valid_end;		
					//var parts = Located_At.split('\n');

					//var lastPart = parts[parts.length - 1];
					//var afterComma = lastPart.split(', ')[1]; 
					document.getElementById("TIN_number").value = data.TIN;
					document.getElementById("Licence_Number").value = data.Licence_Number;
					document.getElementById("Expires").value = data.Expires;
					document.getElementById("Valid_Period").value = data.Valid_end;
					document.getElementById("Issue_Date").value = data.Valid_start;
					/* if (afterComma == 'New Providence') {
						var billingStateSelect = document.getElementById("billing_state");
						billingStateSelect.value = "NP";
						billingStateSelect.disabled = true;
					} */
					document.getElementById("Business_Trading_As_Name").value = data.Trading_As;
					document.getElementById("Business_Address").value = data.Located_At;
					document.getElementById("Business_Type").value = data.Business_Type;
					if (data.Trading_As && data.Located_At && data.Business_Type) {
						document.getElementById("Business_Trading_As_Name").readOnly = true;
						document.getElementById("Business_Address").readOnly = true;
						document.getElementById("Business_Type").readOnly = true;
					}
					jQuery("#showdiv_first").show();
					jQuery("#showdiv").show();
					jQuery("#showdiv_second").show(); 
				}
			}
        }); 
    });
	
	jQuery(document).on('click',"#nextButton", function() {
		var isValid = true;
		
		jQuery(".errormsg").remove();
		var cityValue = jQuery("#showdiv_first input[name='city']").val().trim();
		if (cityValue === "") {
			isValid = false;
			jQuery("#billing_city_field").append('<div class="errormsg">Business City/Settlement/Pairish is required.</div>');
		} else {
			jQuery("#billing_city_field .errormsg").remove();
		}
		var billing_state = jQuery("#showdiv_first #billing_state").val();
		if (billing_state === "") {
			isValid = false;
			jQuery("#billing_state_field").append('<div class="errormsg">Business City/Country/Island is required.</div>');
		} else {
			jQuery("#billing_state_field .errormsg").remove();
		}
		
		var ttyyp = jQuery('input:radio[name="terms"]:checked').val();
		if(ttyyp == 'Yes'){
			var TIN_number = jQuery("#showdiv #TIN_number").val();
			if (TIN_number === "") {
				isValid = false;
				jQuery("#user_tin_number").append('<div class="errormsg">TIN Number is required.</div>');
			} else {
				jQuery("#user_tin_number .errormsg").remove();
			}
			
			var Licence_Number = jQuery("#showdiv #Licence_Number").val();
			if (Licence_Number === "") {
				isValid = false;
				jQuery("#user_licence_Number").append('<div class="errormsg">Licence Number is required.</div>');
			} else {
				jQuery("#user_licence_Number .errormsg").remove();
			}
			
			var Expires = jQuery("#showdiv #Expires").val();
			if (Expires === "") {
				isValid = false;
				jQuery("#user_licence_Expires").append('<div class="errormsg">Licence Expires is required.</div>');
			} else {
				jQuery("#user_licence_Expires .errormsg").remove();
			}
			
			var Valid_Period = jQuery("#showdiv #Valid_Period").val();
			if (Valid_Period === "") {
				isValid = false;
				jQuery("#user_Valid_Period").append('<div class="errormsg">Licence Valid Period is required.</div>');
			} else {
				jQuery("#user_Valid_Period .errormsg").remove();
			}
			
		}

        if (isValid) {
			if(ttyyp == 'Yes'){
				jQuery("#showdiv, #showdiv_first, #showdiv, #nextButton").hide();
				jQuery("#notice_message").hide();
				jQuery("#targetDivmain").hide();
				jQuery("#image_data").hide();
				jQuery("#showdiv_second").show();
			}else{
				jQuery("#showdiv_first, #nextButton").hide();
				jQuery("#targetDivmain").show();
				jQuery("#notice_message").hide();
				jQuery("#showdiv_second").show();
			}
        }
    });
	jQuery(document).on('click',"#backButton", function() {
		alert("okay");
		var isValid = true;
		
		jQuery(".errormsg").remove();
		var cityValue = jQuery("#showdiv_first input[name='city']").val().trim();
		if (cityValue === "") {
			isValid = false;
			jQuery("#billing_city_field").append('<div class="errormsg">Business City/Settlement/Pairish is required.</div>');
		} else {
			jQuery("#billing_city_field .errormsg").remove();
		}
		var billing_state = jQuery("#showdiv_first #billing_state").val();
		if (billing_state === "") {
			isValid = false;
			jQuery("#billing_state_field").append('<div class="errormsg">Business City/Country/Island is required.</div>');
		} else {
			jQuery("#billing_state_field .errormsg").remove();
		}
		
		var ttyyp = jQuery('input:radio[name="terms"]:checked').val();
		if(ttyyp == 'Yes'){
			var TIN_number = jQuery("#showdiv #TIN_number").val();
			if (TIN_number === "") {
				isValid = false;
				jQuery("#user_tin_number").append('<div class="errormsg">TIN Number is required.</div>');
			} else {
				jQuery("#user_tin_number .errormsg").remove();
			}
			
			var Licence_Number = jQuery("#showdiv #Licence_Number").val();
			if (Licence_Number === "") {
				isValid = false;
				jQuery("#user_licence_Number").append('<div class="errormsg">Licence Number is required.</div>');
			} else {
				jQuery("#user_licence_Number .errormsg").remove();
			}
			
			var Expires = jQuery("#showdiv #Expires").val();
			if (Expires === "") {
				isValid = false;
				jQuery("#user_licence_Expires").append('<div class="errormsg">Licence Expires is required.</div>');
			} else {
				jQuery("#user_licence_Expires .errormsg").remove();
			}
			
			var Valid_Period = jQuery("#showdiv #Valid_Period").val();
			if (Valid_Period === "") {
				isValid = false;
				jQuery("#user_Valid_Period").append('<div class="errormsg">Licence Valid Period is required.</div>');
			} else {
				jQuery("#user_Valid_Period .errormsg").remove();
			}
			
		}

        if (isValid) {
			if(ttyyp == 'Yes'){
				jQuery("#showdiv, #showdiv_first, #showdiv, #nextButton").hide();
				jQuery("#notice_message").hide();
				jQuery("#targetDivmain").hide();
				jQuery("#image_data").hide();
				jQuery("#showdiv_second").show();
			}else{
				jQuery("#showdiv_first, #nextButton").hide();
				jQuery("#targetDivmain").show();
				jQuery("#notice_message").hide();
				jQuery("#showdiv_second").show();
			}
        }
    });
	
	
	
});




function handleCheckboxClick(checkbox) {
    if (checkbox.id === 'checkbox-product' && checkbox.checked) {
        // Show modal only if the 'Product' checkbox is checked
        document.getElementById('confirmationModal').style.display = 'flex';
    }
	
	if (checkbox.id === 'checkbox-product' && !checkbox.checked) {
    // Loop through the options and show the one with the value '17527'
		var subscriptionPackSelect = document.getElementById('dokan-subscription-pack');
		for (var i = 0; i < subscriptionPackSelect.options.length; i++) {
			if (subscriptionPackSelect.options[i].value === '17527') {
				subscriptionPackSelect.options[i].style.display = 'block'; // or 'inline' or 'whatever the default display is'
			}
		}
    }
}

function toggleOkButton() {
        var checkbox = document.getElementById('confirmationCheckbox');
        var okButton = document.getElementById('okButton');
        // Enable the "OK" button if the checkbox is checked, otherwise disable it
        okButton.disabled = !checkbox.checked;
}


function handleOk() {
    // Do nothing here for now, you may add further actions if needed
    document.getElementById('confirmationModal').style.display = 'none';

    // Loop through the options and hide the one with the value '17527'
	var subscriptionPackSelect = document.getElementById('dokan-subscription-pack');
    for (var i = 0; i < subscriptionPackSelect.options.length; i++) {
        if (subscriptionPackSelect.options[i].value === '17527') {
            subscriptionPackSelect.options[i].style.display = 'none';
        }
    }
}

function handleCancel() {
    // If user clicks 'Cancel', uncheck both checkboxes
    document.getElementById('checkbox-service').checked = false;
    document.getElementById('checkbox-product').checked = false;
    document.getElementById('confirmationModal').style.display = 'none';
}

function handleRadioClick(choice) {
    var service = document.getElementById('checkbox-service');
    var checkbox = document.getElementById('checkbox-product');
    var common = document.getElementById('checkbox-common-product');

    if (choice === 'yes') {
        service.disabled = false; // enable the radio button
        checkbox.disabled = false; // enable the radio button
        common.disabled = false; // enable the radio button
    } else if (choice === 'no') {
        service.disabled = true; // disable the radio button
        checkbox.disabled = true; // disable the radio button
        common.disabled = true; // disable the radio button
    }
}


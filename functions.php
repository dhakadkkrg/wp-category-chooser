<?php

/**
 * Enqueue script and styles for child theme
 */
function my_custom_enqueue_styles()
{
    $handle = 'wc-country-select';
    wp_enqueue_script($handle, get_site_url() . '/wp-content/plugins/woocommerce/assets/js/frontend/country-select.min.js', array('jquery'), true);

    wp_enqueue_style('child-style2', 'REMOVED_LINK');
    wp_enqueue_style('child-style3', 'REMOVED_LINK');
    wp_enqueue_style('child-style4', 'REMOVED_LINK');
    wp_enqueue_style('child-style5', 'REMOVED_LINK');

    wp_enqueue_script('custom_js2', 'REMOVED_LINK');
    wp_enqueue_script('custom_js3', 'REMOVED_LINK');
    wp_enqueue_script('custom_js4', 'REMOVED_LINK');
    wp_enqueue_script('custom_js', get_stylesheet_directory_uri() . '/custom.js');

    $countries = WC()->countries->get_countries();
    $states = WC()->countries->get_states();

    // Localize the custom script and pass the states variable to it
    $script_data_array = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('file_upload'),
        'woocommerce_states' => $states,
    );
    wp_localize_script('custom_js', 'blog', $script_data_array);
}
add_action('wp_enqueue_scripts', 'my_custom_enqueue_styles', 10010);

/*Extra field on the seller settings and show the value on the store banner -Dokan*/

function my_custom_scripts_enqueue()
{
    wp_enqueue_script('custom-scripts', trailingslashit(get_stylesheet_directory_uri()) . '/cst-nav.js', array('jquery'), null, true);
}

//jeet
// Register the shortcode with WordPress
add_shortcode('gc_vendor_registration', 'gc_vendor_registration');
/**
 * Vendor Registration Shortcode
 *
 * This shortcode function generates a registration form for vendors.
 *
 * @return string The HTML markup for the vendor registration form.
 */
function gc_vendor_registration()
{
    // Initialize variables
	$errors = array();
    $step1_register = true;
    $addon_step = false;
    $checkout_step = false;
    $thankyou_step = false;
    $login_step = false;
    $user_id = 0;
    $role = '';
    $subscriptio = '';
    $form_submit = false;

    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get current user ID and user object
        $user_id = get_current_user_id();
        $user = wp_get_current_user();
        // Check if user is a seller
        if (in_array('seller', (array) $user->roles)) {
            $role = 'seller';
        }

        // Check if user has active subscription
        $subscriptions = wcs_get_users_subscriptions($user_id, array('status' => 'active', 'limit' => -1));

        if (!empty($subscriptions)) {
            // User has active subscription products
            $subscriptio = 'yes';
        } else {
            // User does not have active subscription products
            $subscriptio = 'no';
        }
    }

    // If user does not have an active subscription and is a seller
    if ($subscriptio == 'no' && $role == 'seller' && !empty($user_id)) {
        // Update step flags
        $step1_register = false;
        $addon_step = true;
    }

    // Set image URLs
    $image_url = get_stylesheet_directory_uri() . '/register/aurekaimg.png';
    $mailbox = get_stylesheet_directory_uri() . '/register/mailbox.png';
    $id_placeholder_img = get_stylesheet_directory_uri() . '/register/id-verification.jpg';

    // Vendor login
    if (isset($_POST['login'])) {
        $login = $_POST['login'];
        $username2 = $_POST['username'];
        $password2 = $_POST['password'];

        $creds = array(
            'user_login' => $username2,
            'user_password' => $password2,
            'remember' => true,
        );

        // Sign in user
        $user = wp_signon($creds, false);

        // Check if sign-in was successful
        if (is_wp_error($user)) {
            // Sign-in failed
            echo $user->get_error_message();
            $login_step = true;
            $addon_step = false;
        } else {
            // Get user ID and roles
            $user_id = $user->ID;
            $user_roles = $user->roles;

            // Check if user is a seller
            if (in_array('seller', (array) $user_roles)) {
                $role = 'seller';
            }

            // Check if user has active subscription
            $subscriptions = wcs_get_users_subscriptions($user_id, array('status' => 'active', 'limit' => -1));

            if (!empty($subscriptions)) {
                // User has active subscription products
                $subscriptio = 'yes';
            } else {
                // User does not have active subscription products
                $subscriptio = 'no';
            }

            // If user does not have an active subscription and is a seller
            if ($subscriptio == 'no' && $role == 'seller' && !empty($user_id)) {
                // Update step flags
                $step1_register = false;
                $addon_step = true;
            }
        }
    }

    if (isset($_POST['step1_submit'])) {
        // Retrieve form data
        // Sanitize inputs
		$storeName = isset($_POST['store_name']) ? sanitize_text_field($_POST['store_name']) : '';
		$businessLicense = isset($_POST['business_license']) ? sanitize_text_field($_POST['business_license']) : '';
		$businessCountry = isset($_POST['business_country']) ? sanitize_text_field($_POST['business_country']) : '';
		$businessCity = isset($_POST['business_city']) ? sanitize_text_field($_POST['business_city']) : '';
		$businessSettlement = isset($_POST['businessSettlement']) ? sanitize_text_field($_POST['businessSettlement']) : '';
		$telephone_Number = isset($_POST['telephone_number']) ? sanitize_text_field($_POST['telephone_number']) : '';
		$industry = isset($_POST['industry']) ? sanitize_text_field($_POST['industry']) : '';
		$sub_categories = isset($_POST['sub_categories']) ? $_POST['sub_categories'] : ''; // Not sanitized yet
		$first_name = isset($_POST['owner_first_name']) ? sanitize_text_field($_POST['owner_first_name']) : '';
		$last_name = isset($_POST['owner_last_name']) ? sanitize_text_field($_POST['owner_last_name']) : '';
		$username = isset($_POST['business_email']) ? sanitize_email($_POST['business_email']) : '';
		$email = $username; // Email could be sanitized here if required
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
		$businessTelephone = isset($_POST['business_telephone']) ? sanitize_text_field($_POST['business_telephone']) : '';
		$id_card = isset($_POST['id_card']) ? sanitize_text_field($_POST['id_card']) : '';


		// Validate inputs
		if (empty($storeName)) {
			$errors[] = 'Store name is required';
		}

		if (empty($businessLicense)) {
			$errors[] = 'Business license is required';
		}

        // Validate business license
              if (empty($_POST['business_license'])) {
                  $errors[] = "Business license is required.";
              }

              // Validate business country
              if (empty($businessCountry)) {
                  $errors[] = "Business country is required.";
              }

              // Validate business city
              if (empty($_POST['business_city'])) {
                  $errors[] = "Business city is required.";
              }

              // Validate business settlement
              if (empty($businessSettlement)) {
                  $errors[] = "Business settlement is required.";
              }

              // Validate telephone number
              if (empty($telephone_Number)) {
                  $errors[] = "Telephone number is required.";
              } 
			  /* elseif (!is_numeric($_POST['telephone_number'])) {
                  $errors[] = "Telephone number must be numeric.";
              } */

              // Validate industry
              if (empty($_POST['industry'])) {
                  $errors[] = "Industry is required.";
              }

              // Validate owner first name
              if (empty($_POST['owner_first_name'])) {
                  $errors[] = "Owner's first name is required.";
              }

              // Validate owner last name
              if (empty($_POST['owner_last_name'])) {
                  $errors[] = "Owner's last name is required.";
              }
      
        // Validate business email
        if (empty($_POST['business_email'])) {
            $errors[] = "Business email is required.";
        } elseif (!filter_var($_POST['business_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // Validate password
              if (empty($_POST['password'])) {
                  $errors[] = "Password is required.";
              }

              // Validate confirm password
              if (empty($_POST['confirm_password'])) {
                  $errors[] = "Confirm password is required.";
              } elseif ($_POST['password'] !== $_POST['confirm_password']) {
                  $errors[] = "Password and confirm password do not match.";
              }

              // Validate business telephone
              if (empty($_POST['business_telephone'])) {
                  $errors[] = "Business telephone is required.";
              } 
			  /* elseif (!is_numeric($_POST['business_telephone'])) {
                  $errors[] = "Business telephone must be numeric.";
              } */

        // Check if there are any errors before proceeding
        if (!empty($errors)) {
            $step1_register = true;
            $addon_step = false;
            $checkout_step = false;
            $thankyou_step = false;
            $login_step = false;
        } else {
			 $step1_register = false;
            $addon_step = false;
            $checkout_step = false;
            $thankyou_step = false;
            $login_step = true;
            $form_submit = true;
        

        //upload id image
        $upload_dir = wp_upload_dir(); // Get the WordPress uploads directory
        $target_dir = $upload_dir['basedir'] . '/custom-folder/'; // Define the target directory

        // Create the target directory if it doesn't exist
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $target_file = $target_dir . basename($_FILES['id_card']['name']);
        $upload_url = '';
        if (!empty($_FILES['id_card']['tmp_name']) && is_uploaded_file($_FILES['id_card']['tmp_name'])) {
            if (move_uploaded_file($_FILES['id_card']['tmp_name'], $target_file)) {
                // Retrieve the URL of the uploaded image
                $upload_url = $upload_dir['baseurl'] . '/custom-folder/' . basename($_FILES['id_card']['name']);

            }
        }


        //create request
        $post_id = wp_insert_post(
            array(
                'post_type' => 'vendor_request',
                'post_status' => 'publish',
                'post_author' => 149,
                'post_title' => $storeName,
                'post_content' => 'Lorem ipsum'
            )
        );

        if ($post_id) {
            $meta_data = array(
                'store_name' => $storeName,
                'business_license' => $businessLicense,
                'business_country' => $businessCountry,
                'business_city' => $businessCity,
                'business_settlement' => $businessSettlement,
                'telephone_number' => $telephone_Number,
                'industry' => $industry,
                'owner_first_name' => $first_name,
                'owner_last_name' => $last_name,
                'business_telephone' => $businessTelephone,
                'business_email' => $email,
                'password' => $password,
                'confirm_password' => $confirmPassword,
                'id_img_url' => $upload_url,



            );

            foreach ($meta_data as $meta_key => $meta_value) {
                add_post_meta($post_id, $meta_key, $meta_value);
            }
            echo '<div class="gc_success_msg">Your information has been submitted for review.</div>';
		}
        } /* else {
            echo '<div class="gc_error_msg">Registration failed. Please try again.</div>';
        } */
    }
    $step_class = "";
    $step_addons = "";
    if ($step1_register == true) {
        $step_class = "Gc-activestep";
    }
    if ($addon_step == true) {
        $step_class = "Gc-activestep";
        $step_addons = "Gc-activestep";
    }
    /* echo 's='.$step1_register;
       echo '<br>';
       echo 'aa='.$addon_step;
       echo '<br>';
       echo $checkout_step;
       echo '<br>';
       echo $thankyou_step;
       echo '<br>';
       echo $login_step; */
    ?>
    <?php if ($login_step == false) { ?>
        <div id="Gc-container">

            <div id="Gc-steps">
                <h1>Create Your Online Store</h1>
                <div class="Gc-step <?php echo $step_class; ?>">Enter Store Details</div>
                <div class="Gc-step <?php echo $step_class; ?>">Business Owner Verification</div>
                <div class="Gc-step <?php echo $step_addons; ?>">Select Add Ons</div>
                <div class="Gc-step">Chekout</div>
                <div class="Gc-step">ThankYou</div>
            </div>

            <div id="Gc-form">

                <!-- Step 1 form -->
                <?php if ($step1_register == true) { ?>
                    <form method="post" id="gc-form" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>"
                        enctype="multipart/form-data">
                        <div id="gc-step1Form" class="Gc-step-form Gc-active">
                            <h1>Step 1. Enter Store details</h1>
							<?php
							// Display errors to the user
							if (!empty($errors)) {
								foreach ($errors as $error) {
									echo '<div class="gc_error_msg">'.$error.'</div>';
									
								}
							}
							
							?>
                            <div class="Gc-main-content">
                                <div class="Gc-industry">

                                    <label>What would you like your Store to be named?</label>
                                    <strong id="url-alart-mgs" class="pull-right text-success"></strong>
                                    <input type="text" class="Gc-inputbox" name="store_name" id="gc_store_name" required>
                                    <small id="gc-url-alart"></small>

                                </div>
                                <label>Do you have a Business Liecense?</label>
                                <div class="Gc-Yesfeild">
                                    <label>
                                        <input type="radio" name="business_license" value="yes" onclick="showFields('yes')"> Yes
                                    </label>
                                    <label>
                                        <input type="radio" name="business_license" value="no" onclick="showFields('no')" checked>
                                        No
                                    </label>
                                </div>


                                <div id="yesFields" class="gc-hidden">
                                    <div>

                                        <div class="Gc-license-frist">
                                            <label for="licenseupload">Upload Your License</label> <br>
                                            <input type="file" id="licenseupload" name="licenseupload">
                                        </div>


                                        <div class="Gc-industry">
                                            <label for="businessIndustry">Industry</label>
                                            <input type="text" id="businessIndustry" name="businessIndustry">
                                        </div>

                                        <div class="Gc-businessCountry">
                                            <div>
                                                <label for="Gc-businessCountry-lic">Business Country*</label>
                                                <select id="Gc-businessCountry-lic" class="Gc-inputbox" name="businessCountry_yes">
                                                    <option value="USA">United States</option>
                                                    <option value="Canada">Canada</option>
                                                    <option value="UK">United Kingdom</option>
                                                    <!-- Add more options as needed -->
                                                </select>
                                            </div>


                                            <div>
                                                <label for="Gc-businessCountry-yes">Business State/Island/Province/Parish*</label>
                                                <select id="Gc-businessCountry-yes" class="Gc-inputbox">
                                                    <option value="USA">India</option>
                                                    <option value="Canada">Canada</option>
                                                    <option value="UK">United Kingdom</option>
                                                    <!-- Add more options as needed -->
                                                </select>
                                            </div>

                                        </div>

                                        <div class="Gc-industry">
                                            <label for="businessCity">Business City/Settlement/Capital*</label>
                                            <input type="text" id="businessCity" name="businessCity">

                                        </div>

                                        <div class="Gc-businessCountry">
                                            <div> <label for="businessTelephone">Business Telephone</label>
                                                <input type="text" id="businessTelephone" class="gc-telephone-number"
                                                    name="businessTelephone">
                                            </div>

                                            <div>
                                                <label for="averageOrderVolume">Average Daily Order Volume</label>
                                                <input type="number" id="averageOrderVolume" name="averageOrderVolume">
                                            </div>

                                        </div>

                                        <div class="Gc-industry">
                                            <label for="businessName">Business Name*</label>
                                            <input type="text" id="businessName" name="businessName">
                                        </div>

                                        <div class="Gc-businessCountry">
                                            <div> <label for="tradingName">Business Trading As Name*</label>
                                                <input type="text" id="tradingName" name="tradingName">
                                            </div>
                                            <div>
                                                <label for="businessType">Business Type*</label>
                                                <input type="text" id="businessType" name="businessType">
                                            </div>

                                        </div>


                                    </div>

                                    <div>

                                        <div class="Gc-businessCountry">
                                            <div> <label for="businessTIN">Business TIN Number*</label>
                                                <input type="text" id="businessTIN" name="businessTIN">
                                            </div>

                                            <div> <label for="businessLicenseNumber">Business License Number*</label>
                                                <input type="text" id="businessLicenseNumber" name="businessLicenseNumber">
                                            </div>

                                        </div>

                                        <div class="Gc-businessCountry">
                                            <div>
                                                <label for="licenseExpiry">Business License Expiry*</label>
                                                <input type="text" id="licenseExpiry" name="licenseExpiry">
                                            </div>

                                            <div>
                                                <label for="licensePeriod">Business License Valid Period*</label>
                                                <input type="text" id="licensePeriod" name="licensePeriod">
                                            </div>

                                        </div>

                                        <div class="Gc-businessCountry">
                                            <div>
                                                <label for="licenseIssueDate">Business Issue Date*</label>
                                                <input type="text" id="licenseIssueDate" name="licenseIssueDate">
                                            </div>

                                            <div>
                                                <label for="businessAddress">Business Address*</label>
                                                <input type="textarea" id="businessAddress" name="businessAddress" rows="1"></input>
                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <!-- yes field end -->

                                <!-- no field start -->

                                <div id="noFields">
                                    <?php
                                    // Get country and state arrays from WooCommerce
                                    $countries = WC()->countries->get_countries();
                                    $states = WC()->countries->get_states();
                                    // Output the registration form with country and state dropdowns
                                    ?>
                                    <div class="Gc-businessCountry">
                                        <div>
                                            <label for="businessCountry">Business Country*</label>
                                            <select id="Gc-businessCountry" name="business_country">
                                                <?php foreach ($countries as $code => $name): ?>
                                                    <option value="<?php echo esc_attr($code); ?>">
                                                        <?php echo esc_html($name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div id="state_field">
                                            <label for="Gc-businessState">Business State/Island/Province/Parish*</label>
                                            <select id="Gc-businessState" name="businessSettlement">
                                                <option value="">Select a state</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="Gc-telnumber">
                                        <label>Telephone Number</label>
                                        <!-- <input type="hidden" name="formatted_phone" id="formatted_phone"> -->
                                        <input type="tel" id="Gc-telephone-number" class="Gc-inputbox gc-telephone-number"
                                            name="telephone_number">
                                    </div>
									<?php
									// Get all product categories
									$product_categories = get_terms( array(
										'taxonomy'   => 'product_cat',
										'hide_empty' => false,
									) );
									
									?>
                                    <div class="Gc-telnumber">
                                        <label>Industry</label>
                                     <select id="gc-industry" name="industry" class="Gc-select">
										<option value="">Select Industry</option>
										<?php
										// Loop through each product category and display as options
										foreach ($product_categories as $category) {
											echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
										}
										?>
										</select>
                                    </div>
									<div class="Gc-telnumber" id="gc-subcategory">
                                        <label for="subcategory">Subcategory:</label>
									  
									  <select id="subcategory">
										<option value="" name="sub_categories" class="Gc-select">Select a subcategory</option>
										<!-- Subcategories will be populated dynamically using JavaScript -->
									  </select>
                                    </div>
                                </div>
                            </div>
                            <div class="Gc-navigation-buttons">
                                <button class="Gc-next-button" type="button" onclick="nextStep()">Next</button>
                            </div>
                        </div>

                        <!-- Step 2 form -->
                        <div id="Gc-step2Form" class="Gc-step-form">
                            <h1>Step 2. Account Owner Verification</h1>
                            <div class="Gc-main-content">
                                <div class="Gc-business-country">
                                    <div class="Gc-country">
                                        <div>
                                            <label for="owner_first_name">Owner's First Name</label>
                                            <input type="text" class="Gc-inputbox" id="owner_first_name" name="owner_first_name">
                                        </div>
                                        <div>
                                            <label for="owner_last_name">Owner's Last Name</label>
                                            <input type="text" class="Gc-inputbox" id="owner_last_name" name="owner_last_name">
                                        </div>
                                    </div>
                                    <div class="Gc-country">
                                        <div>
                                            <label for="business_email">Business Email</label>
                                            <input type="email" class="Gc-inputbox" id="business_email" name="business_email">
                                        </div>
                                        <div>
                                            <label for="business_city">Business City/Country/Island</label>
                                            <input type="text" class="Gc-inputbox" id="business_city" name="business_city">
                                        </div>
                                    </div>
                                    <div class="Gc-country">
                                        <div>
                                            <label for="password">Password</label>
                                            <input type="password" class="Gc-inputbox" id="gc_password" name="password">
                                        </div>
                                        <div>
                                            <label for="confirm_password">Confirm Password</label>
                                            <input type="password" class="Gc-inputbox" id="confirm_password"
                                                name="confirm_password">
                                            <span id="gc-message"></span><br>
                                        </div>
                                    </div>
                                    <div>
                                        <label>Business Telephone</label>
                                        <input type="tel" class="Gc-inputbox gc-telephone-number" id="business_telephone"
                                            name="business_telephone">
                                    </div>
                                </div>
                            </div>
                                <div class="gc-main-content">
                                <div class="gc-idverifyclass">Id Verification</div>
                                <div class="gc-idverifyclass">Select Id Type:</div>
                                <label>
                                    <input type="radio" name="id-verify" value="Passport-ID" onclick="showIdentity()"> Passport
                                </label>
                                <label>
                                    <input type="radio" name="id-verify" value="National-ID" onclick="showIdentity()"> National ID card
                                </label>
                                <label>
                                    <input type="radio" name="id-verify" value="Driving-ID" onclick="showIdentity()"> Driving License
                                </label>
                                <div id="gc-choose-id" class="gc-hidden">
                                </div>
                            </div>
                            <div class="Gc-main-content">
                                <label for="declarationCheckbox">Declaration</label>
                                <div>
                                    <input type="checkbox" id="declarationCheckbox">
                                    <span>I declare that the information provided is accurate and true.</span>
                                </div>
                            </div>
                            <div class="Gc-navigation-buttons">
                                <button class="Gc-previous-button" type="button" onclick="previousStep()">Previous</button>
                                <button class="Gc-next-button" name="step1_submit" id="step1_submit" type="sbumit"
                                    disabled>Save</button>
                            </div>
                        </div>
                    </form>
                <?php } ?>
                <!-- step1 form end -->

                <!-- step3 form start -->
                <?php if ($addon_step == true) { ?>
                    <form id="step4Form" class="Gc-step-form" style="display: block;">
                        <div class="gc-wrapper">
                            <div class="gc-license">
                                <h1>Choose Your License</h1>
                            </div>
                            <div class="gc-container">
                                <div>
                                    <div class="gc-card">

                                        <div class="gc-enter-busi">

                                            <h2>
                                                <span><i class="fa-solid fa-business-time"></i></span>Business
                                            </h2>
                                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quam natus, accusantium,
                                                saepe numquam, culpa quas eos officia placeat hic dolorum libero nesciunt ut non
                                                dolor unde! Expedita veniam iure perspiciatis!</p>
                                            <h3>Feature</h3>
                                            <ul>
                                                <li>Benefit 1</li>
                                                <li>Benefit 2</li>
                                                <li>Benefit 3</li>
                                            </ul>
                                            <div class="gc-read-more">
                                                <!-- <p><a href="#">Read more</a></p> -->
                                                <input type="button" class="Gc-add-to-cart" name="button"
                                                    value="Add to Cart"></input>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div>
                                    <div class="gc-card">

                                        <div class="gc-enter-busi">

                                            <h2>
                                                <span><i class="fa-solid fa-business-time"></i></span>Enterprize
                                            </h2>
                                            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quam natus, accusantium,
                                                saepe numquam, culpa quas eos officia placeat hic dolorum libero nesciunt ut non
                                                dolor unde! Expedita veniam iure perspiciatis!</p>
                                            <h3>Feature</h3>
                                            <ul>
                                                <li>Benefit 1</li>
                                                <li>Benefit 2</li>
                                                <li>Benefit 3</li>
                                            </ul>

                                            <!-- <p><a href="#">Read more</a></p> -->
                                            <input type="button" class="Gc-add-to-cart" name="button" value="Contact US"></input>
                                        </div>

                                    </div>

                                </div>
                            </div>


                            <div class="gc-addon-container">
                                <div class="gc-addon">
                                    <div class="gc-addon-title">
                                        <p>Price: $10.00</p>
                                    </div>
                                    <div class="gc-addon-title">
                                        <p>Name: Addon 1</p>
                                    </div>
                                    <div class="gc-quantity-controls">
                                        <button class="gc-decrement">-</button>
                                        <input type="number" value="1" class="gc-quantity-input Gc-quantity" name="number">
                                        <button class="gc-increment">+</button>
                                    </div>
                                    <input type="button" class="Gc-add-to-cart Gc_add_btn" name="button" value="Add to Cart"
                                        data-product-id="20512"></input>
                                </div>
                            </div>
                        </div>
                        <div class="Gc-navigation-buttons">
                            <button class="Gc-previous-button" type="button" onclick="previousStep()">Previous</button>
                            <button class="Gc-next-button" type="button" onclick="nextStep()">Next</button>
                        </div>
                    </form>
                <?php } ?>
                <!-- step3 form end -->
                <!-- step4 checout end form start -->
                <?php if ($checkout_step == true) { ?>
                    <form id="step4Form" class="Gc-step-form" style="display: block;">
                        <h1>Checkout</h1>
                        <?php
                        ob_start();
                        echo do_shortcode('[woocommerce_checkout]');
                        ?>
                        <div class="Gc-navigation-buttons">
                            <button class="Gc-previous-button" type="button" onclick="previousStep()">Previous</button>
                            <button class="Gc-next-button" type="button" onclick="nextStep()">Next</button>
                        </div>
                    </form>
                <?php } ?>
                <!-- step4 checout end form start -->
                <?php if ($thankyou_step == true) { ?>
                    <form id="step5Form" class="Gc-step-form">
                        <h1>I am step 5</h1>
                        <div class="Gc-navigation-buttons">
                            <button class="Gc-previous-button" type="button" onclick="previousStep()">Previous</button>
                            <button class="Gc-next-button" type="button" onclick="nextStep()">Next</button>
                        </div>
                    </form>
                <?php } ?>

                <form id="lastStepForm" class="Gc-step-form">
                    <h1>Last Step. Submit Form</h1>
                    <div class="Gc-navigation-buttons">
                        <button class="Gc-previous-button" type="button" onclick="previousStep()">Previous</button>
                        <button class="Gc-submit-button" type="button" onclick="submitForm()">Submit</button>
                    </div>
                </form>
            </div>
            <div id="Gc-image">
                <img src="<?php echo $image_url; ?>" alt="Static Image">
            </div>
        </div>
    <?php } ?>
    <?php

    if ($login_step) {
        ?>
        <div class="gc-login-form">
            <h2 class="wd-login-title">Login</h2>
            <form method="post" class="login woocommerce-form woocommerce-form-login" action="
            <?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide form-row-username">
                    <label for="username">Username or email address&nbsp; <span class="required">*</span>
                    </label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username"
                        id="username" value="">
                </p>
                <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide form-row-password">
                    <label for="gc-password-login">Password&nbsp; <span class="required">*</span>
                    </label>
                    <span class="password-input">
                        <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password"
                            id="gc-password-login">
                    </span>
                </p>
                <p class="form-row">
                    <input type="hidden" id="woocommerce-login-nonce" name="woocommerce-login-nonce" value="be663e059c">
                    <input type="hidden" name="_wp_http_referer" value="/staging/9630/my-account/">
                    <button type="submit" class="button woocommerce-button woocommerce-form-login__submit" name="login"
                        value="Log in">Log in</button>
                </p>
            </form>
        </div>

        <?php
    }
    ?>

    <!-- Popup -->
    <div class="Gc-overlay" id="Gc-overlay"></div>
    <div class="Gc-popup" id="Gc-popup">
        <span class="Gc-close-btn-x" onclick="hidePopup()">X</span>
        <div class="Gc-popup-content">
            <h1>Success</h1>
            <p class="Gc-review">Your information has been submitted for review.</p>
            <div class="Gc-message">
                <p class="Gc-receivemsg">You will receive an email within the next 48 hours with instructions to proceed
                    with your subscription.</p>

                <div class="email-recieve">
                    <!-- Email box icon -->
                    <span><img src="<?php echo $mailbox; ?>"></span><br>
                    <!-- Learn more about our subscription plans -->
                    <p class="Gc-plan">Learn more about our subscription plans</p><br>
                </div>

                <!-- Visit plans button -->
                <button class="Gc-visit">Visit Plans</button>
            </div>
        </div>
    </div>

    <script>
        <?php
        if (($login_step == true) && ($form_submit == true)) {
            ?>
            showPopup();

            <?php
        }
        ?>
		 jQuery(function(){
		  jQuery("#gc-industry").select2();
		 });
		 
		

        // Add this script to enable/disable the "Next" button based on checkbox state
        document.getElementById('declarationCheckbox').addEventListener('change', function () {
            document.getElementById('step1_submit').disabled = !this.checked;
        });

        const forms = document.querySelectorAll('.Gc-step-form');
        const steps = document.querySelectorAll('.Gc-step');
        const imageContainer = document.getElementById('Gc-image');

        let currentStep = 0;

        // steps.forEach((step, index) => {
        //     step.addEventListener('click', () => {
        //         currentStep = index;
        //         updateSteps();
        //     });
        // });

        function updateSteps() {
            steps.forEach((step, index) => {
                if (index === currentStep) {
					if (step && forms[index]) {
						step.classList.add('Gc-activestep');
						forms[index].classList.add('Gc-active');
					}
                } else {
					if (step && forms[index]) {
						step.classList.remove('Gc-active');
						forms[index].classList.remove('Gc-active');
					}
                    
                }
            });
        }

        function nextStep() {

            if (currentStep < forms.length - 1) {
                currentStep++;
                updateSteps();


            }

        }

        function previousStep() {
            if (currentStep > 0) {
                currentStep--;
                updateSteps();
            }
        }

        const step1_submit = document.getElementById('step1_submit');
        const declarationCheckbox = document.getElementById('declarationCheckbox');

        if (declarationCheckbox && step1_submit) {
            declarationCheckbox.addEventListener('change', function () {
                step1_submit.disabled = !declarationCheckbox.checked;

                // Check the condition and update styles accordingly
                if (declarationCheckbox.checked) {
                    step1_submit.style.backgroundColor = '#01395E';
                    step1_submit.style.color = '#fff';
                    step1_submit.style.padding = '10px 20px';
                    step1_submit.style.border = 'none';
                    step1_submit.style.borderRadius = '4px';
                    step1_submit.style.cursor = 'pointer';
                } else {
                    // Reset styles if the condition is not true
                    step1_submit.style.backgroundColor = ''; // Reset to default
                    step1_submit.style.color = ''; // Reset to default
                    step1_submit.style.padding = '10px 20px'; // Reset to default
                    step1_submit.style.border = ''; // Reset to default
                    step1_submit.style.borderRadius = ''; // Reset to default
                    step1_submit.style.cursor = ''; // Reset to default
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const quantityInputs = document.querySelectorAll('.gc-quantity-input');
            const incrementButtons = document.querySelectorAll('.gc-increment');
            const decrementButtons = document.querySelectorAll('.gc-decrement');

            // Function to handle increment button click
            function handleIncrement(index) {
                quantityInputs[index].value = parseInt(quantityInputs[index].value) + 1;
            }

            // Function to handle decrement button click
            function handleDecrement(index) {
                if (quantityInputs[index].value > 1) {
                    quantityInputs[index].value = parseInt(quantityInputs[index].value) - 1;
                }
            }

            // Attach click event listeners to increment and decrement buttons
            incrementButtons.forEach((button, index) => {
                button.addEventListener('click', () => handleIncrement(index));
            });

            decrementButtons.forEach((button, index) => {
                button.addEventListener('click', () => handleDecrement(index));
            });
        });

        function submitForm() {

            // Add your submission logic here
            alert('Form submitted successfully!');

        }



        function showPopup() {
            document.getElementById('Gc-overlay').style.display = 'block';
            document.getElementById('Gc-popup').style.display = 'block';
        }

        function hidePopup() {
            document.getElementById('Gc-overlay').style.display = 'none';
            document.getElementById('Gc-popup').style.display = 'none';
        }

        function showFields(choice) {
            var yesFields = document.getElementById('yesFields');
            var noFields = document.getElementById('noFields');

            if (choice === 'yes') {
                yesFields.classList.remove('gc-hidden');
                noFields.classList.add('gc-hidden');
            } else if (choice === 'no') {
                yesFields.classList.add('gc-hidden');
                noFields.classList.remove('gc-hidden');
            }
        }
        // function handleFileChange(event) {
        //     var chosenFileSpan = document.querySelector('.chosen-file');

        //     // Display the chosen image
        //     if (event.target.files[0]) {
        //         var img = document.createElement('img');
        //         img.src = URL.createObjectURL(event.target.files[0]);
        //         img.alt = 'Chosen Image';
        //         img.style.maxWidth = '50px'; // Set a maximum width
        //         img.style.height = 'auto'; // Maintain aspect ratio

        //         chosenFileSpan.innerHTML = ''; // Clear any existing content
        //         chosenFileSpan.appendChild(img);
        //     } else {
        //         chosenFileSpan.innerHTML = ''; // Clear content if no file is selected
        //     }
        // }
        function showIdentity() {
            var chooseIdDiv = document.getElementById('gc-choose-id');
            chooseIdDiv.innerHTML = ''; 
            
            var label = document.createElement('label');
            label.classList.add('gc-custom-file-input');
            label.innerHTML = 'Upload an Image';

            var inputFile = document.createElement('input');
            inputFile.type = 'file';
            inputFile.id = 'gc-fileInput'; 
            inputFile.name = 'id_card';
            inputFile.accept = 'image/*';
            inputFile.addEventListener('change', handleFileChange); 
            
            label.setAttribute('for', 'gc-fileInput');

            var labeltakephoto = document.createElement('label');
            labeltakephoto.classList.add('gc-custom-file-input');
            labeltakephoto.innerHTML = 'Take Photo';

            var fileContainer = document.createElement('div');
            fileContainer.classList.add('gc-file-container');
            fileContainer.appendChild(labeltakephoto);
            fileContainer.appendChild(label);
            fileContainer.appendChild(inputFile);

            var chosenFileSpan = document.createElement('span');
            chosenFileSpan.classList.add('gc-choosen-file');
            fileContainer.appendChild(chosenFileSpan);

            var dragText = document.createElement('div');
            dragText.classList.add('gc-drag-text');
            dragText.innerHTML = 'or Drag & Drop here';
            fileContainer.appendChild(dragText);

            fileContainer.addEventListener('dragover', handleDragOver);
            fileContainer.addEventListener('drop', handleDrop);

            var logoContainer = document.createElement('div');
            logoContainer.classList.add('gc-logo-container');

            var gcimglogo = document.createElement('img');
            gcimglogo.classList.add('gc-id-logo-image');
            gcimglogo.setAttribute('src', '<?php echo $id_placeholder_img ; ?>');

            logoContainer.appendChild(gcimglogo);

            chooseIdDiv.classList.remove('gc-hidden');
            chooseIdDiv.appendChild(logoContainer);
            chooseIdDiv.appendChild(fileContainer);
        }

        function handleDragOver(event) {
            event.preventDefault();
            var fileContainer = event.currentTarget;
            fileContainer.classList.add('dragover');
        }

        function handleDrop(event) {
            event.preventDefault();
            var fileContainer = event.currentTarget;
            fileContainer.classList.remove('dragover');

            var files = event.dataTransfer.files;
            if (files.length > 0) {
                handleFileChange({ target: { files: [files[0]] } });
            }
        }

        function handleFileChange(event) {
            var chosenFileSpan = document.querySelector('.gc-choosen-file');

            // Display the chosen image
            if (event.target.files[0]) {
                var img = document.createElement('img');
                var reader = new FileReader();

                reader.onload = function (e) {
                    img.src = e.target.result;

                    chosenFileSpan.innerHTML = '';
                    chosenFileSpan.appendChild(img);

                    var logoreplacebyimg = document.querySelector(".gc-logo-container");
                    logoreplacebyimg.innerHTML = '';  // Clear existing content
                    logoreplacebyimg.appendChild(chosenFileSpan);
                };

                reader.readAsDataURL(event.target.files[0]);
            } else {
                chosenFileSpan.innerHTML = '';
            }
        }
    
            // if (chooseIdDiv) {
            //     chooseIdDiv.classList.add('gc-show-id');
            //     chooseIdDiv.appendChild(fileContainer);
            //     chooseIdDiv.style.display = 'block';
            // } else {
            //     console.error("chooseIdDiv is undefined.");
            // }
        

        //tel
        // Get all elements with the specified class
        // var phoneInputs = document.querySelectorAll(".gc-telephone-number");

        // // Iterate over each phone input element
        // phoneInputs.forEach(function(phone_input) {
        //     // Create formatted phone input element for each phone input
        //     var formatted_phone_input = document.getElementById("formatted_phone");

        //     // Initialize intlTelInput for each phone input
        //     var iti = window.intlTelInput(phone_input, {
        //         utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        //     });

        //     // Format phone number on focus out (blur) for each phone input field
        //     phone_input.addEventListener('blur', function (e) {
        //         var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        //         var formatted_phone = '(' + x[1] + ') ' + x[2] + (x[2] ? '-' : '') + x[3];

        //         // Get the selected country code
        //         var countryCode = iti.getSelectedCountryData().dialCode;

        //         // Update the value of the formatted phone input field
        //         formatted_phone_input.value = "+" + countryCode + " " + formatted_phone;

        //         // Display the formatted phone number and country code
        //         phone_input.value = "+" + countryCode + " " + formatted_phone;
        //     });
        // });
        // number code

    </script>
    <script>
                 /* var inputs = document.querySelectorAll(".gc-telephone-number, .business-telephone-number");

inputs.forEach(function (input) {
    var iti = window.intlTelInput(input, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    input.addEventListener('input', function (e) {
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        e.target.value = '(' + x[1] + ') ' + x[2] + (x[2] ? '-' : '') + x[3];
    });
}); */ 

// document.getElementById("gc-form").addEventListener('submit', function (e) {
//     e.preventDefault();
//     var input = document.querySelector(".gc-telephone-number");
//     var dialCode = iti.getSelectedCountryData().dialCode;
//     var phoneNumber = "+" + dialCode + input.value;
//     document.getElementById("phoneValue").innerText = "Submitted Phone Number: " + phoneNumber;
// });
    </script>

    <?php
    // Get country and state arrays from WooCommerce
    $countries = WC()->countries->get_countries();
    $states = WC()->countries->get_states();
    // Output the registration form with country and state dropdowns

    ?>

    <?php
}
/**
 * AJAX handler for adding a product to the cart.
 *
 * Handles AJAX requests to add a product to the WooCommerce cart.
 *
 * @since 1.0.0
 */
add_action('wp_ajax_gc_get_sub_category_name', 'gc_get_sub_category_name');
add_action('wp_ajax_nopriv_gc_get_sub_category_name', 'gc_get_sub_category_name');

/**
 * Adds a product to the cart via AJAX.
 *
 * @since 1.0.0
 */
function gc_get_sub_category_name()
{
	$category_id = isset($_POST['cat_id']) ? sanitize_text_field(wp_unslash($_POST['cat_id'])) : '';
	
	// Check if category ID is valid
	if ($category_id <= 0) {
		// Return an empty response if the category ID is not valid
		echo json_encode(array('error' => 'Invalid category ID'));
		exit;
	}
	
	$subcategories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'parent'     => $category_id,
        'hide_empty' => false,
    ) );
	
	// Check if subcategories array is empty
	if (empty($subcategories)) {
		// Return a response indicating that there are no subcategories available
		echo json_encode(array('error' => 'No subcategories found for the provided category ID'));
		exit;
	}

    // Prepare data to send back as JSON
    $subcategory_data = array();
    foreach ($subcategories as $subcategory) {
        $subcategory_data[] = array(
            'id'   => $subcategory->term_id,
            'name' => $subcategory->name,
        );
    }

    // Send subcategory data as JSON response
    echo json_encode($subcategory_data);
	exit;
}
/**
 * AJAX handler for adding a product to the cart.
 *
 * Handles AJAX requests to add a product to the WooCommerce cart.
 *
 * @since 1.0.0
 */
add_action('wp_ajax_gc_get_stor_name', 'gc_get_stor_name');
add_action('wp_ajax_nopriv_gc_get_stor_name', 'gc_get_stor_name');

/**
 * Adds a product to the cart via AJAX.
 *
 * @since 1.0.0
 */
function gc_get_stor_name()
{
    /* if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), 'dokan_reviews' ) ) {
               wp_send_json_error(
                   [
                       'type'    => 'nonce',
                       'message' => __( 'Are you cheating?', 'dokan-lite' ),
                   ]
               );
           } */

    //global $user_ID;

    $url_slug = isset($_POST['url_slug']) ? sanitize_text_field(wp_unslash($_POST['url_slug'])) : '';
    $check = true;
    $user = get_user_by('slug', $url_slug);

    if (false !== $user) {
        $check = false;
    }
    if ($check) {
        wp_send_json_success(
            [
                'message' => __('Available', 'dokan-lite'),
                'url' => home_url() . '/store/' . sanitize_user($url_slug),
            ]
        );
    } else {
        wp_send_json_success(
            [
                'message' => __('Not Available', 'dokan-lite'),
                'url' => false,
            ]
        );
    }

}
/**
 * AJAX handler for adding a product to the cart.
 *
 * Handles AJAX requests to add a product to the WooCommerce cart.
 *
 * @since 1.0.0
 */
add_action('wp_ajax_gc_custome_add_to_cart', 'gc_custome_add_to_cart');
add_action('wp_ajax_nopriv_gc_custome_add_to_cart', 'gc_custome_add_to_cart');

/**
 * Adds a product to the cart via AJAX.
 *
 * @since 1.0.0
 */
function gc_custome_add_to_cart()
{


    // Check if product_id and quantity are set in the request
    if (isset($_POST['product_id'], $_POST['quantity'])) {
        // Sanitize and validate product_id and quantity
        $product_id = absint($_POST['product_id']);
        $quantity = absint($_POST['quantity']);
        WC()->cart->empty_cart();
        // Add the product to the cart
        //$result = WC()->cart->add_to_cart($product_id, $result, 0, array(), $cart_item_data);

        //$result = WC()->cart->add_to_cart($product_id, $quantity);


        $product_id = 20215; // Example product ID
        $quantity = 1;
        $variation_id = 0;
        $cart_item_data = array();
        $cart_item_data['custom_key'] = 'custom_value'; // Replace with your custom data
        $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $cart_item_data);



        // Prepare response message
        if ($result) {
            $response = array(
                'status' => true,
                'message' => __('Product added successfully.', 'your-text-domain')
            );
        } else {
            $response = array(
                'status' => false,
                'message' => __('Failed to add product to the cart.', 'your-text-domain')
            );
        }
    } else {
        // Invalid request, set error response
        $response = array(
            'status' => false,
            'message' => __('Invalid request. Please provide product ID and quantity.', 'your-text-domain')
        );
    }

    // Send JSON response
    wp_send_json($response);
}

// Add custom data to cart item
function add_custom_cart_item_data($cart_item_data, $product_id, $variation_id)
{
    // Check if the product is the one we want to add custom data to
    if ($product_id == 20512) {
        // Define your custom data
        $cart_item_data3[] = array(
            'name' => 'Boost revenue potential with add Ons',
            'value' => 'Self',
            'price' => 30,
            'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
            'field_type' => 'checkbox',
            'price_type' => 'flat_fee',
        );
        $cart_item_data['addons'] = $cart_item_data3;
    }

    return $cart_item_data;
}
//add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data', 10, 3);

add_shortcode('abcd', 'abcd');
function abcd()
{








    WC()->cart->empty_cart();
    // Add product to the cart with custom data
    $product_id = 20635; // Example product ID
    $quantity = 1;
    $variation_id = 0;
    $cart_item_data = array();
    $cart_item_data['custom_key'] = 'custom_value'; // Replace with your custom data
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $cart_item_data);

    // Output any additional content you want to display
    $output = '<p>Product added to cart with custom data.</p>';

    return $output;












    //$result = WC()->cart->add_to_cart(20512, 1);

    /* $cart_item_data = array(
          'addons' => array(
              array(
                  'name'       => 'Boost revenue potential with add Ons',
                  'value'      => 'Self',
                  'price'      => 30,
                  'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                  'field_type' => 'checkbox',
                  'price_type' => 'flat_fee',
              )
          )
      ); */
    /* $cart_item_data=array('addons'=>array());
   $cart_item_data3[] = array(
                       'name'       => 'Boost revenue potential with add Ons',
                       'value'      => 'Self',
                       'price'      => 30,
                       'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                       'field_type' => 'checkbox',
                       'price_type' => 'flat_fee',
                   );
                   $cart_item_data['addons'] = $cart_item_data3;
      $items = WC()->cart->add_to_cart(20512, 1, 0, array(), $cart_item_data); */



























    /* $cart_item_data = array(
        'addons' => array(
            'name'       => 'Boost revenue potential with add Ons',
            'value'      => 'Self',
            'price'      => 30,
            'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
            'field_type' => 'checkbox',
            'price_type' => 'flat_fee',
        )
    ); 
     global $woocommerce;
     WC()->cart->add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ); */
    //$result = WC()->cart->add_to_cart(20512, 1);

    /* $cart_item_data3[] = array(
                        'name'       => 'Boost revenue potential with add Ons',
                        'value'      => 'Self',
                        'price'      => 30,
                        'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                        'field_type' => 'checkbox',
                        'price_type' => 'flat_fee',
                    ); */
    //$item['addons']=$cart_item_data;

    // Merge the custom data with existing cart item data
    //$cart_item_data['addons'] = $cart_item_data3;



    //$result = WC()->cart->add_to_cart(20215, 1, 0, array(), $cart_item_data);

    //$product_addons = WC_Product_Addons_Helper::get_product_addons( 20512 );

    //$items = $woocommerce->cart->get_cart();


    // Prepare cart item data with addons
    /* $cart_item_data = array(
        'addons' => array(
            array(
                'name'       => 'Boost revenue potential with add Ons',
                'value'      => 'Self',
                'price'      => 30,
                'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                'field_type' => 'checkbox',
                'price_type' => 'flat_fee',
            )
        )
    ); */
    //$cart_item_data['selection'][] = 'abc';
    // Add to cart
    // $items = WC()->cart->add_to_cart(20512, 1, 0, array(), $cart_item_data);
// Check if WooCommerce is active
    /* if ( class_exists( 'WooCommerce' ) ) {
        // Prepare cart item data with addons
        $cart_item_data = array(
            'addons2' => array(
                'name'       => 'Boost revenue potential with add Ons',
                'value'      => 'Self',
                'price'      => 30,
                'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                'field_type' => 'checkbox',
                'price_type' => 'flat_fee',
            )
        );

        // Add to cart
        $product_id = 20512;
        $quantity = 1;
        $variation_id = 0;
        $variation = array(); // If adding a variation
        WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );

        // Redirect to the cart page
        wp_redirect( wc_get_cart_url() );
        exit;
    } else {
        return 'WooCommerce is not active.';
    }

    echo '<pre>';
    print_r($items);
    echo '</pre>'; */

    /* echo '<pre>';
       print_//r($result);
       echo '</pre>'; */
    /* global $woocommerce;
       $product_addons = WC_Product_Addons_Helper::get_product_addons( 20512 );
       
       $items = $woocommerce->cart->get_cart();
       echo '<pre>';
       print_r($items);
       echo '</pre>';
       foreach($items as $item){
           if ( empty( $item['addons'] ) ) {
               $item['addons'] = array();
                $cart_item_data[] = array(
                       'name'       => 'Boost revenue potential with add Ons',
                       'value'      => 'Self',
                       'price'      => 30,
                       'field_name' => '20512-boost-revenue-potential-with-add-ons-0',
                       'field_type' => 'checkbox',
                       'price_type' => 'flat_fee',
                   );
                   $item['addons']=$cart_item_data;
           } */
    /* if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
          foreach ( $product_addons as $addon ) {
              
          }
          }
          
          
           */
    /* echo '<pre>';
     print_r($items);
     echo '</pre>'; */

    //require_once '/home2/apjjykmy/public_html/staging/9630/wp-content/plugins/woocommerce-product-addons/includes/fields/class-wc-product-addons-field.php';

    //include_once dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php';
    //$field = new WC_Product_Addons_Field_List( $addon, 'checkbox' );
    /* echo '<pre>';
             print_r($field);
             echo '</pre>'; */
    //}

    //}

    //}


}

/**
 * Add custom checkout fields before customer details in WooCommerce checkout page.
 */
function my_custom_checkout_fields()
{
    $custom_data = '';
    // Get cart contents
    $cart = WC()->cart->get_cart();

    foreach ($cart as $cart_item_key => $cart_item) {
        // Get product name
        $product_name = $cart_item['data']->get_name();

        // Check if the 'variation' array exists and if the 'custom_key' exists within it
        if (isset($cart_item['variation']['custom_key'])) {
            // Get custom data associated with the item
            $custom_data = $cart_item['variation']['custom_key']; // Change 'custom_key' to match the key you used
        }
    }

    if ($custom_data == 'custom_value') {
        ?>
        <style>
            .checkout-order-review {
                margin: auto;
            }
        </style>
        <div id="Gc-steps">
            <h1>Create Your Online Store</h1>
            <div class="Gc-step Gc-activestep">Enter Store Details</div>
            <div class="Gc-step Gc-activestep">Business Owner Verification</div>
            <div class="Gc-step Gc-activestep">Select Add Ons</div>
            <div class="Gc-step Gc-activestep">Checkout</div>
            <div class="Gc-step">Thankyou</div>
        </div>
        <?php
    }
}
//add_action('woocommerce_checkout_before_customer_details', 'my_custom_checkout_fields');



/**
 * Disable Payment Method for Dokan Subscription Product
 * if a key exists in cart
 */
//add_filter( 'lpac_override_map_visibility', 'gc_unset_gateway_by_category2', 99 );
function gc_unset_gateway_by_category2()
{
    return 0;
}

// Define a function to remove the filter
function remove_checkout_map_filter($abcd)
{
    exit;

}

// Hook the removal function to a suitable action
// For example, you might hook it to 'init' to ensure it's removed early enough
//add_action('lpac_before_checkout_map_container', 'remove_checkout_map_filter');
// Hook into a later action to ensure the action is added before removal
//add_action('after_setup_theme', 'remove_checkout_map_action');

function remove_checkout_map_action()
{
    // Remove the action added in the parent theme or plugin
    remove_action('woocommerce_checkout_before_customer_details', 'Main', 'outputCheckoutMap');
}

//add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields($fields)
{
    //unset($fields['billing']['billing_email']);
    // unset($fields['shipping']['shipping_first_name']);

    return $fields;
}


// Conditional check to include custom checkout template
function include_custom_checkout_template()
{
    if (is_checkout()) {
        // Load the custom checkout template
        wc_get_template('checkout/form-checkout.php');
        //exit; // Stop further execution
    }
}
//add_action( 'template_redirect', 'include_custom_checkout_template' );
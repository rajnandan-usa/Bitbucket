<div id="contact-form-container">
    <form id="contact-form" method="post">
        <div>
            <label for="first-name">First Name *</label>
            <input type="text" name="first_name" id="first-name" required>
        </div>
        <div>
            <label for="last-name">Last Name *</label>
            <input type="text" name="last_name" id="last-name" required>
        </div>
        <div>
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="phone-number">Phone Number *</label>
            <input type="tel" name="phone_number" id="phone-number" required>
        </div>
        <div>
            <label for="address">Address</label>
            <input type="text" name="address" id="address">
        </div>
        <div>
            <input type="submit" value="Submit">
        </div>
        <?php wp_nonce_field('save_contact_nonce', 'save_contact_nonce'); ?>
    </form>
    <div id="contact-form-message"></div>
</div>

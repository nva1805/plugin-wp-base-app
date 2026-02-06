/**
 * Register Form - Alpine.js component
 * Uses shared form-validator.js
 */

const registerForm = () =>
	createFormValidator({
		fields: {
			username: "",
			email: "",
			password: "",
			password_confirm: "",
		},
		rules: {
			username: [
				ValidationRules.required("Username is required"),
				ValidationRules.minLength(
					3,
					"Username must be at least 3 characters",
				),
				ValidationRules.alphanumeric(
					"Only letters, numbers and underscores",
				),
			],
			email: [
				ValidationRules.required("Email is required"),
				ValidationRules.email("Please enter a valid email"),
			],
			password: [
				ValidationRules.required("Password is required"),
				ValidationRules.minLength(8, "At least 8 characters"),
			],
			password_confirm: [
				ValidationRules.required("Please confirm password"),
				ValidationRules.matches("password", "Passwords do not match"),
			],
		},
		init() {
			const usernameInput = document.getElementById("username");
			const emailInput = document.getElementById("email");

			if (usernameInput?.value) this.form.username = usernameInput.value;
			if (emailInput?.value) this.form.email = emailInput.value;
		},
	});

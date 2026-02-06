/**
 * Login Form - Alpine.js component
 * Uses shared form-validator.js
 */

const loginForm = () =>
	createFormValidator({
		fields: {
			username: "",
			password: "",
			remember: false,
		},
		rules: {
			username: [
				ValidationRules.required("Username or email is required"),
			],
			password: [ValidationRules.required("Password is required")],
		},
		init() {
			const usernameInput = document.getElementById("username");
			if (usernameInput?.value) this.form.username = usernameInput.value;
		},
	});

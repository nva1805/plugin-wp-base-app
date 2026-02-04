console.log("register.js loaded");

const registerForm = () => ({
	form: {
		username: "",
		email: "",
		password: "",
		password_confirm: "",
	},
	errors: {
		username: "",
		email: "",
		password: "",
		password_confirm: "",
	},
	touched: {
		username: false,
		email: false,
		password: false,
		password_confirm: false,
	},
	rules: {
		username: [
			{ test: (v) => !!v.trim(), message: "Username is required" },
			{
				test: (v) => v.trim().length >= 3,
				message: "Username must be at least 3 characters",
			},
			{
				test: (v) => /^[a-zA-Z0-9_]+$/.test(v),
				message: "Only letters, numbers and underscores",
			},
		],
		email: [
			{ test: (v) => !!v.trim(), message: "Email is required" },
			{
				test: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
				message: "Please enter a valid email",
			},
		],
		password: [
			{ test: (v) => !!v, message: "Password is required" },
			{
				test: (v) => v.length >= 8,
				message: "At least 8 characters",
			},
		],
		password_confirm: [
			{ test: (v) => !!v, message: "Please confirm password" },
			{
				test: (v, form) => v === form.password,
				message: "Passwords do not match",
			},
		],
	},

	validateField(field) {
		this.touched[field] = true;
		this.errors[field] = "";

		const value = this.form[field];
		const rules = this.rules[field] || [];

		for (const rule of rules) {
			if (!rule.test(value, this.form)) {
				this.errors[field] = rule.message;
				return false;
			}
		}
		return true;
	},

	validateAll() {
		let isValid = true;
		for (const field of Object.keys(this.rules)) {
			this.touched[field] = true;
			if (!this.validateField(field)) {
				isValid = false;
			}
		}
		return isValid;
	},

	submitForm(event) {
		event.preventDefault();
		if (!this.validateAll()) {
			this.$nextTick(() => {
				const firstError = document.querySelector(".input-error");
				if (firstError) firstError.focus();
			});
			return;
		}
		event.target.submit();
	},

	hasError(field) {
		return this.touched[field] && this.errors[field];
	},

	clearError(field) {
		this.errors[field] = "";
	},

	init() {
		const usernameInput = document.getElementById("username");
		const emailInput = document.getElementById("email");

		if (usernameInput?.value) this.form.username = usernameInput.value;
		if (emailInput?.value) this.form.email = emailInput.value;
	},
});

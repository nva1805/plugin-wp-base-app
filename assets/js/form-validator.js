/**
 * Base Form Validator - Shared Alpine.js form utilities
 * Provides common validation logic for all forms
 */

const createFormValidator = (config) => ({
	form: config.fields || {},
	errors: Object.keys(config.fields || {}).reduce(
		(acc, key) => ({ ...acc, [key]: "" }),
		{},
	),
	touched: Object.keys(config.fields || {}).reduce(
		(acc, key) => ({ ...acc, [key]: false }),
		{},
	),
	rules: config.rules || {},

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

	// Override in specific forms if needed
	init() {
		if (config.init) {
			config.init.call(this);
		}
	},
});

// Common validation rules factory
const ValidationRules = {
	required: (message = "This field is required") => ({
		test: (v) => !!String(v).trim(),
		message,
	}),

	minLength: (min, message) => ({
		test: (v) => String(v).trim().length >= min,
		message: message || `Must be at least ${min} characters`,
	}),

	maxLength: (max, message) => ({
		test: (v) => String(v).trim().length <= max,
		message: message || `Must be no more than ${max} characters`,
	}),

	email: (message = "Please enter a valid email") => ({
		test: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
		message,
	}),

	pattern: (regex, message = "Invalid format") => ({
		test: (v) => regex.test(v),
		message,
	}),

	matches: (fieldName, message = "Fields do not match") => ({
		test: (v, form) => v === form[fieldName],
		message,
	}),

	alphanumeric: (
		message = "Only letters, numbers and underscores allowed",
	) => ({
		test: (v) => /^[a-zA-Z0-9_]+$/.test(v),
		message,
	}),
};

(function($) {
  'use strict';

  $(document).ready(function() {
    const $form = $('.register-container form');
    const $username = $('#username');
    const $email = $('#email');
    const $password = $('#password');
    const $passwordConfirm = $('#password_confirm');
    const $container = $('.register-container');

    // Remove existing error on input focus
    $form.find('input').on('focus', function() {
      $(this).removeClass('input-error');
      $(this).closest('.form-group').find('.field-error').remove();
    });

    // Real-time validation
    $username.on('blur', function() {
      validateUsername();
    });

    $email.on('blur', function() {
      validateEmail();
    });

    $password.on('blur', function() {
      validatePassword();
    });

    $passwordConfirm.on('blur', function() {
      validatePasswordConfirm();
    });

    // Form submit validation
    $form.on('submit', function(e) {
      clearErrors();
      
      let isValid = true;

      if (!validateUsername()) isValid = false;
      if (!validateEmail()) isValid = false;
      if (!validatePassword()) isValid = false;
      if (!validatePasswordConfirm()) isValid = false;

      if (!isValid) {
        e.preventDefault();
        showMainError('Please fix the errors below');
        // Focus first error field
        $form.find('.input-error').first().focus();
      }
    });

    function validateUsername() {
      const value = $username.val().trim();
      
      if (!value) {
        showFieldError($username, 'Username is required');
        return false;
      }
      
      if (value.length < 3) {
        showFieldError($username, 'Username must be at least 3 characters');
        return false;
      }

      if (!/^[a-zA-Z0-9_]+$/.test(value)) {
        showFieldError($username, 'Username can only contain letters, numbers and underscores');
        return false;
      }

      return true;
    }

    function validateEmail() {
      const value = $email.val().trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (!value) {
        showFieldError($email, 'Email is required');
        return false;
      }
      
      if (!emailRegex.test(value)) {
        showFieldError($email, 'Please enter a valid email address');
        return false;
      }

      return true;
    }

    function validatePassword() {
      const value = $password.val();
      
      if (!value) {
        showFieldError($password, 'Password is required');
        return false;
      }
      
      if (value.length < 8) {
        showFieldError($password, 'Password must be at least 8 characters');
        return false;
      }

      // Check password strength
      const hasLetter = /[a-zA-Z]/.test(value);
      const hasNumber = /\d/.test(value);
      
      if (!hasLetter || !hasNumber) {
        showFieldError($password, 'Password must contain letters and numbers');
        return false;
      }

      return true;
    }

    function validatePasswordConfirm() {
      const password = $password.val();
      const confirm = $passwordConfirm.val();
      
      if (!confirm) {
        showFieldError($passwordConfirm, 'Please confirm your password');
        return false;
      }
      
      if (password !== confirm) {
        showFieldError($passwordConfirm, 'Passwords do not match');
        return false;
      }

      return true;
    }

    function showFieldError($field, message) {
      $field.addClass('input-error');
      const $formGroup = $field.closest('.form-group');
      $formGroup.find('.field-error').remove();
      $formGroup.append('<span class="field-error">' + message + '</span>');
    }

    function showMainError(message) {
      // Remove existing main error
      $container.find('.error.js-error').remove();
      // Add new error after h1
      $container.find('h1').after('<div class="error js-error">' + message + '</div>');
    }

    function clearErrors() {
      $form.find('.input-error').removeClass('input-error');
      $form.find('.field-error').remove();
      $container.find('.error.js-error').remove();
    }

    // Password strength indicator
    $password.on('input', function() {
      const value = $(this).val();
      const $formGroup = $(this).closest('.form-group');
      
      // Remove existing strength indicator
      $formGroup.find('.password-strength').remove();
      
      if (value.length > 0) {
        const strength = getPasswordStrength(value);
        $formGroup.append('<div class="password-strength ' + strength.class + '">' + strength.text + '</div>');
      }
    });

    function getPasswordStrength(password) {
      let score = 0;
      
      if (password.length >= 8) score++;
      if (password.length >= 12) score++;
      if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
      if (/\d/.test(password)) score++;
      if (/[^a-zA-Z0-9]/.test(password)) score++;

      if (score <= 1) return { class: 'strength-weak', text: 'Weak' };
      if (score <= 2) return { class: 'strength-fair', text: 'Fair' };
      if (score <= 3) return { class: 'strength-good', text: 'Good' };
      return { class: 'strength-strong', text: 'Strong' };
    }
  });
})(jQuery);

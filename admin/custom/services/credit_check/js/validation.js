
$(document).ready(function() {
    $('#investigation_form').bootstrapValidator({
        // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            tenant_surname: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Please supply your surname'
                    }
                }
            },
            tenant_firstname: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Please supply your first name'
                    }
                }
            },
            tenant_email: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your email address'
                    },
                    emailAddress: {
                        message: 'Please supply a valid email address'
                    }
                }
            },
            tenant_dateofbirth: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your date of birth'
                    }
                }
            },
            tenant_address: {
                validators: {
                   stringLength: {
                        min: 4,
                    },
                    notEmpty: {
                        message: 'Please supply your address'
                    }
                }
            },
            tenant_tellandlord: {
                validators: {
                    stringLength: {
                        min: 4,
                    },
                    notEmpty: {
                        message: "Please supply your landlord's tel"
                    }
                }
            },
            tenant_endlease: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your lease end date'
                    }
                }
            },
            tenant_rent: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your rent'
                    }
                }
            },
            banking_limit: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your credit limit'
                    }
                }
            }
        }
    })
    .on('success.form.bv', function(e) {
            $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...


            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post('action.php', $form.serialize(), function(result) {
                console.log(result);
            }, 'json');

            $('#investigation_form').bootstrapValidator('resetForm', true);
        });
});

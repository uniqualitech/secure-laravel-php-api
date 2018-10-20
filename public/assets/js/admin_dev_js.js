var matched, browser;

jQuery.uaMatch = function (ua) {
    ua = ua.toLowerCase();

    var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
            /(webkit)[ \/]([\w.]+)/.exec(ua) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
            /(msie) ([\w.]+)/.exec(ua) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
            [];

    return {
        browser: match[ 1 ] || "",
        version: match[ 2 ] || "0"
    };
};

matched = jQuery.uaMatch(navigator.userAgent);
browser = {};

if (matched.browser) {
    browser[ matched.browser ] = true;
    browser.version = matched.version;
}

// Chrome is Webkit, but Webkit is also Safari.
if (browser.chrome) {
    browser.webkit = true;
} else if (browser.webkit) {
    browser.safari = true;
}

jQuery.browser = browser;



function validateLogin() {
    var email = $("#email").val();
    var password = $("#password").val();

    var css_error = ({
        'border-color': 'red'
    });
    var css_original = ({
        'border-color': 'rgb(204, 204, 204)'
    });

    $("#email").css(css_original);
    $("#password").css(css_original);

    var filter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (email == "" || !filter.test(email)) {
        $("#email").css(css_error);
        return false;
    } else if (password == "") {
        $("#password").css(css_error);
        return false;
    } else {
        return true;
    }
}

function validateChangePassword() {
    var password = $("#password").val();
    var re_password = $("#re-password").val();

    var isReturn = "true";

    var css_error = ({
        'border-color': 'red'
    });
    var css_original = ({
        'border-color': 'rgb(204, 204, 204)'
    });

    $("#password").css(css_original);
    $("#re-password").css(css_original);
    $("#change_pwd_error").hide();


    if (password == "") {
        $("#password").css(css_error);
        isReturn = "false";
        return false;
    } else if (re_password == "") {
        $("#re-password").css(css_error);
        isReturn = "false";
        return false;
    }
    if (password != re_password) {
        $("#password").css(css_error);
        $("#re-password").css(css_error);
        $("#char_error").hide();
        $("#change_pwd_error").show();

        isReturn = "false";
        return false;
    }

    if (isReturn == "true") {
        $("#change_password").submit();
    }
}



function DeleteUser(id) {
    if (confirm("Are you sure want to delete this User?")) {
        $.ajax({
            url: 'admin_ops.php',
            type: 'post',
            data: {
                'action': 'delete_user',
                'user_id': id
            },
            success: function (res) {
                $("#user_" + id).slideUp('slow');
            }
        });
    }
}




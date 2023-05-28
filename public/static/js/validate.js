var validateOption = {
    rules: {},
    messages: {},
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    }
}

var addAdminFormRule = {
    username: {
        required: true,
        minlength: 3,
        maxlength: 12,
        //url: true,
    },
    password: {
        required: true,
        password: true,
        minlength: 6,
        maxlength: 16,
    },
};

var addAdminFormMsg = {
    username: {
        required: "用户名不能为空",
        minlength: "用户名长度为3-12位字符",
        maxlength: "用户名长度为3-12位字符",
        //url: "链接格式不正确"
    },
    password: {
        required: "密码不能为空",
        password: "密码格式不正确",
        minlength: "密码长度为6-16位字符",
        maxlength: "密码长度为6-16位字符",
    },
};
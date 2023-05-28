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

var addCrontabFormRule = {
    url: {
        required: true,
        url: true,
    },
    player: {
        required: true
    },
    title: {
        required: true
    },
    ltNum: {
        required: true,
        number: true,
        min: 0,
    },
    voteNum: {
        required: true,
        number: true,
        min: 1000,
    },
    speed: {
        required: true,
        number: true,
        min: 0,
    },
};

var addCrontabFormMsg = {
    url: {
        required: "链接不能为空",
        url: "链接格式不正确"
    },
    player: {
        required: "选手不能为空"
    },
    title: {
        required: "标题不能为空",
    },
    ltNum: {
        required: "连投次数不能为空",
        number: "连投次数只能为数字",
        min: "连投次数最小为0",
    },
    voteNum: {
        required: "票数不能为空",
        number: "票数只能为数字",
        min: "票数最小为1000",
    },
    speed: {
        required: "限速不能为空",
        number: "限速只能为数字",
        min: "限速最小为0",
    },
};

var editCrontabFormRule = {
    url: {
        required: true,
        url: true,
    },
    speed: {
        required: true,
        number: true,
        min: 0,
    },
    price: {
        required: true,
        number: true,
        min: 0,
    },
};

var editCrontabFormMsg = {
    url: {
        required: "链接不能为空",
        url: "链接格式不正确"
    },
    speed: {
        required: "限速不能为空",
        number: "限速只能为数字",
        min: "限速最小为0",
    },
    price: {
        required: "单价不能为空",
        number: "单价只能为数字",
        min: "单价不得小于0",
    },
};

var addAgentFormRule = {
    username: {
        required: true,
        minlength: 3,
        maxlength: 12,
        //url: true,
    },
    nickname: {
        //url: true,
    },
    password: {
        required: true,
        password: true,
        minlength: 6,
        maxlength: 16,
    },
    amount: {
        required: true,
        number: true,
        min: 0,
    },
};

var addAgentFormMsg = {
    username: {
        required: "用户名不能为空",
        minlength: "用户名长度为3-12位字符",
        maxlength: "用户名长度为3-12位字符",
        //url: "链接格式不正确"
    },
    nickname: {
        required: "选手不能为空"
    },
    password: {
        required: "密码不能为空",
        password: "密码格式不正确",
        minlength: "密码长度为6-16位字符",
        maxlength: "密码长度为6-16位字符",
    },
    amount: {
        required: "金额不能为空",
        number: "金额只能为数字",
        min: "金额最小为0",
    },
};
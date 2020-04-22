module.exports = {
    root: true,
    env: {
        node: true,
        jest: true
    },
    extends: [
        "eslint:recommended",
        "plugin:vue/recommended"
    ],
    rules: {
        "vue/html-indent": ["error", 4],
        "vue/component-name-in-template-casing": ["error", "PascalCase"],
        "no-console": process.env.NODE_ENV === "production" ? "error" : "off",
        "no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",

        "vue/html-self-closing": ["error", {
            "html": {
                "void": "any",
                "normal": "any",
                "component": "any"
            },
            "svg": "never",
            "math": "never"
        }]
    },
    globals: {
        _: true,
        $: true,
        axios: true,
        NProgress: true,
        moment: true
    },
    parserOptions: {
        parser: "babel-eslint"
    },
    plugins: [
        'vue'
    ],
};

import js from "@eslint/js";
import globals from "globals";
import pluginVue from "eslint-plugin-vue";
// import css from "@eslint/css";
import { defineConfig } from "eslint/config";

export default defineConfig([
  {
    files: ["**/*.{js,mjs,cjs,vue}"],
    plugins: {
      js
    },
    extends: [
      js.configs.recommended,
      pluginVue.configs['flat/recommended'], // pluginVue.configs["flat/essential"],
    ],
    languageOptions: {
      globals: globals.browser
    },
    rules: {
      'no-unused-vars': 'warn',
      'no-undef': 'warn',

      // 'prettier/prettier': 'warn',
      'vue/attributes-order': 'off',
      'vue/html-indent': 'off',
      'vue/multi-word-component-names': 'off',
      'vue/multiline-html-element-content-newline': 'off',
      'vue/singleline-html-element-content-newline': 'off',
      'vue/max-attributes-per-line': 'off',
      'vue/html-self-closing': 'off',
      'vue/html-closing-bracket-newline': 'off',
      'vue/first-attribute-linebreak': 'off',
    },
  },
  // {
  //   files: ["**/*.css"],
  //   plugins: {
  //     css
  //   },
  //   language: "css/css",
  //   extends: ["css/recommended"]
  // },
]);

import Translator, { Replacements, LangOptions } from 'lang.js';
import { VueConstructor } from 'vue';
declare type TranslateFunction = (key: string, replacements?: Replacements, locale?: string) => string;
declare type IgnoreList = Map<string, string[]>;
interface Options extends LangOptions {
    ignore: IgnoreList;
    globalTranslationsKey: string;
}
/**
 * Augments vue.
 */
declare module 'vue/types/vue' {
    interface Vue {
        $lang: () => Translator;
        __: TranslateFunction;
    }
}
/**
 * Adds localization to Vue.
 */
declare const Lang: {
    install: (Vue: VueConstructor, options?: Partial<Options>) => void;
};
export { Lang as default, Lang };

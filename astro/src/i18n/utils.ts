import { defaultLang } from './ui';

export function getLangFromUrl(url: URL) {
    const [, , lang] = url.pathname.split('/');
    return lang || defaultLang;
}

export function useTranslations(lang: string, ui: any = {}) {
    return function t(key: string, placeholders = {}) {
        let translated: string = key

        if (!(key in ui)) {
            console.warn('Missing translation for ' + key);
        } else if (!(lang in ui[key])) {
            console.warn('Missing translation for ' + key + ' in ' + lang);
            if (defaultLang in ui[key]) {
                translated = ui[key][defaultLang];
            }
        } else {
            translated = ui[key][lang];
        }

        for (const [key, value] of Object.entries(placeholders)) {
            console.log(value.expressions);
            translated = translated.replaceAll('{{' + key + '}}', value.expressions)
        }

        return translated;
    }
}


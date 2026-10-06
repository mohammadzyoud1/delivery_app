import en from "./translations/en";
import ar from "./translations/ar";
import { getInitialLanguage } from "./language";

// ==================== Types ====================

export type TranslationLanguage = "English" | "Arabic";

type TranslationObject = typeof en;

// ==================== Translations ====================

const translations: Record<TranslationLanguage, TranslationObject> = {
    English: en,
    Arabic: ar,
};

// ==================== Current Language ====================

export const getCurrentLanguage = (): TranslationLanguage => {
    return getInitialLanguage();
};

// ==================== Translation Helper ====================

export const t = (key: string): string => {
    const language = getCurrentLanguage();
    const translation = translations[language];

    const value = key.split(".").reduce<unknown>((current, part) => {
        if (
            typeof current === "object" &&
            current !== null &&
            part in current
        ) {
            return (current as Record<string, unknown>)[part];
        }

        return undefined;
    }, translation);

    return typeof value === "string" ? value : key;
};

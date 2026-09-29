// ==================== Supported Languages ====================

export type Language = "English" | "Arabic";

// ==================== Storage Key ====================

const LANGUAGE_STORAGE_KEY = "deliver_language";

// ==================== Device Language ====================

export const getDeviceLanguage = (): Language => {
    const browserLanguage = navigator.language.toLowerCase();

    if (browserLanguage.startsWith("ar")) {
        return "Arabic";
    }

    return "English";
};

// ==================== Saved Language ====================

export const getSavedLanguage = (): Language | null => {
    const savedLanguage = localStorage.getItem(LANGUAGE_STORAGE_KEY);

    if (savedLanguage === "English" || savedLanguage === "Arabic") {
        return savedLanguage;
    }

    return null;
};

// ==================== Initial Language ====================

export const getInitialLanguage = (): Language => {
    return getSavedLanguage() ?? getDeviceLanguage();
};

// ==================== Save Language ====================

export const saveLanguage = (language: Language): void => {
    localStorage.setItem(LANGUAGE_STORAGE_KEY, language);
};

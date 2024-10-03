import { createI18n } from "vue-i18n";
import en from "./locales/en.json";
import es from "./locales/es.json";
import hi from "./locales/hi.json";
import ko from "./locales/ko.json";
import sr from "./locales/sr.json";


const messages = {
  en,
  es,
  ko,
  hi,
  sr
};

const i18n = createI18n({
  locale: 'en', // set default locale
  fallbackLocale: 'en',
  messages,
});


console.log("Loaded Messages:", messages);
export default i18n;

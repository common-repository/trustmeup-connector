import { useAppState } from "./context";

/**
 * Get a translatable string.
 */
export const i18n = (slug, context = null) => {
  if (context) {
    slug = `${context}.${slug}`;
  }

  return window.TrustMeUp.strings[slug] ? window.TrustMeUp.strings[slug] : "";
};

/**
 * Get a specific field value coming from the database.
 */
export const getField = (name, defaultValue = "") => {
  return window.TrustMeUp.data.fields[name]
    ? window.TrustMeUp.data.fields[name]
    : defaultValue;
};

/**
 * Are TrustMeUp API credentials set?
 */
export const hasSetApiCredentials = (fields) => {
  return (
    (fields.api_client_id !== "" && fields.api_password !== "") ||
    (fields.api_client_id_beta !== "" && fields.api_password_beta !== "")
  );
};

import axios from "axios";

/**
 * Send an AJAX call to the main plugin API route (POST).
 */
export const doAjaxCall = async (action, data = {}) => {
  return axios
    .post(
      `${window.TrustMeUp.data.api.rest_url}trustmeup/v1/admin/${action}`,
      data,
      {
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": window.TrustMeUp.data.api.nonce,
        },
        timeout: 60 * 1000,
      }
    )
    .then(
      (response) => {
        return Promise.resolve(response.data);
      },
      (error) => {
        return Promise.reject(error);
      }
    );
};

/**
 * Wrapper function calling the AJAX route and returning an object based on the Promise.
 */
export const ajax = async (action, data = {}) => {
  try {
    const payload = await doAjaxCall(action, data);

    return {
      success: true,
      ...payload,
    };
  } catch (error) {
    return {
      error: error.message,
    };
  }
};

/**
 * Refresh products.
 */
export const refreshProducts = async () => {
  return await ajax("products-refresh");
};

/**
 * Resync products.
 */
export const resyncProducts = async () => {
  return await ajax("products-resync");
};

/**
 * Disconnect all products.
 */
export const disconnectProducts = async () => {
  return await ajax("products-disconnect");
};

/**
 * Connect a TMU product to one or many Woo products.
 */
export const connectProduct = async (product_id, selection = []) => {
  return await ajax("products-connect", {
    trustmeup_product: product_id,
    woo_products: selection,
  });
};

/**
 * Disconnect single product.
 */
export const disconnectProduct = async (product_id) => {
  return await ajax("products-disconnect", { product: product_id });
};

/**
 * Save basic fields in an option.
 */
export const saveFields = async (fields) => {
  return await ajax("fields-save", { fields });
};

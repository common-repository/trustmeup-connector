import React, { createContext, useState, useEffect, useContext } from "react";

const defaultContext = {
  fields: {
    ...window.TrustMeUp.data.fields,
  },
  isLoading: false,
  notice: {
    message: null,
    success: false,
    error: false,
    warning: false,
  },
  updateField: () => {},
  setLoading: () => {},
  displayNotice: () => {},
};

export const AppContext = createContext(defaultContext);

export const ContextContainer = ({ children }) => {
  const [fields, updateFields] = useState(defaultContext.fields);
  const [isLoading, setLoading] = useState(defaultContext.isLoading);
  const [notice, setNotice] = useState(defaultContext.notice);

  // Update a specific field in our global state.
  const updateField = (name, value) => {
    updateFields((previousFields) => ({
      ...previousFields,
      [name]: value,
    }));
  };

  // Display a notice message at the top of the page.
  const displayNotice = (message, type = "success") => {
    setNotice((previousNotice) => ({
      ...defaultContext.notice,
      message,
      [type]: true,
    }));
  };

  // Hide notice by resetting it.
  const hideNotice = () => {
    setNotice((previousNotice) => defaultContext.notice);
  };

  // Auto-hide notice after 5s.
  useEffect(() => {
    const hider = setTimeout(hideNotice, 7000);

    return () => {
      clearTimeout(hider);
    };
  }, [notice.message]);

  return (
    <AppContext.Provider
      value={{
        // State.
        fields: fields,
        isLoading: isLoading,
        notice: notice,

        // Actions.
        updateField: updateField,
        setLoading: setLoading,
        displayNotice: displayNotice,
        hideNotice: hideNotice,
      }}
    >
      {children}
    </AppContext.Provider>
  );
};

export const useAppState = () => useContext(AppContext);

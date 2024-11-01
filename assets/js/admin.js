import React from "react";
import { render } from "react-dom";
import AdminPage from "./src/AdminPage";

/**
 * Render the WP Mail Pro admin page.
 */
if (document.querySelector("#tmu-admin-page-container")) {
  const renderAdminPage = () => {
    render(<AdminPage />, document.querySelector("#tmu-admin-page-container"));
  };

  document.addEventListener("DOMContentLoaded", renderAdminPage);
}

import { hot } from "react-hot-loader/root";
import React, { useEffect } from "react";
import { i18n } from "../utils/utils";
import { Snackbar } from "@sparkpost/matchbox";
import { useAppState } from "../utils/context";

const Notice = () => {
  const { notice, hideNotice } = useAppState();

  if (!notice.message) {
    return null;
  }

  let status = "success";

  if (notice.error) status = "error";
  if (notice.warning) status = "warning";

  return (
    <Snackbar
      className="feedback-notice"
      status={status}
      onDismiss={hideNotice}
    >
      {notice.message}
    </Snackbar>
  );
};

const Header = () => {
  return (
    <header className="settings-page-header">
      <h1 className="wp-heading-inline">{i18n("Global.PageTitle")}</h1>
      <Notice />
    </header>
  );
};

export default hot(Header);

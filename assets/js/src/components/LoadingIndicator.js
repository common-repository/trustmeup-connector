import { hot } from "react-hot-loader/root";
import React from "react";
import { i18n } from "../utils/utils";
import { useAppState } from "../utils/context";
import { Spinner } from "@sparkpost/matchbox";

const LoadingIndicator = () => {
  const { isLoading } = useAppState();

  if (!isLoading) {
    return null;
  }

  return (
    <div className="loader">
      <Spinner size="large" label="Loading..." />
    </div>
  );
};

export default hot(LoadingIndicator);

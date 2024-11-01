import { hot } from "react-hot-loader/root";
import React from "react";
import { ThemeProvider } from "@sparkpost/matchbox";

import { ContextContainer } from "./utils/context";
import Header from "./components/Header";
import Tabs from "./components/Tabs";

const AdminPage = () => {
  return (
    <ThemeProvider>
      <ContextContainer>
        <div className="wrap">
          <Header />
          <div className="admin-page-core-content">
            <Tabs />
          </div>
        </div>
      </ContextContainer>
    </ThemeProvider>
  );
};

export default hot(AdminPage);

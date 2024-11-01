import { hot } from "react-hot-loader/root";
import React, { useEffect } from "react";
import { Tabs as MatchboxTabs, useTabs } from "@sparkpost/matchbox";
import LoadingIndicator from "./LoadingIndicator";
import { i18n } from "../utils/utils";
import { Overview, Settings } from "../tabs";
import { useAppState } from "../utils/context";

const tabsList = [
  { title: i18n("Tabs.Overview"), component: Overview, slug: "overview" },
  { title: i18n("Tabs.Settings"), component: Settings, slug: "settings" },
];

const initialTabIndex =
  tabsList.findIndex((tab) => tab.slug === window.TrustMeUp.data.current_tab) ||
  0;

const Tabs = () => {
  const { hideNotice } = useAppState();

  const { tabIndex, setTabIndex, tabs } = useTabs({
    tabs: tabsList.map((tab) => ({
      content: tab.title,
    })),
  });

  useEffect(() => {
    setTabIndex(initialTabIndex);
  }, []);

  const onTabSelect = (tabIndex) => {
    setTabIndex(tabIndex);
    hideNotice();
  };

  return (
    <>
      <nav className="plugin-tabs">
        <MatchboxTabs
          selected={tabIndex}
          tabs={tabs}
          onSelect={onTabSelect}
          fitted
        />
      </nav>

      {tabsList.map((tab, i) => {
        if (i !== tabIndex) {
          return null;
        }

        const TabContent = tab.component;

        return (
          <div className="tab-content" key={`tabContent${i}`}>
            <TabContent />
            <LoadingIndicator />
          </div>
        );
      })}
    </>
  );
};

export default hot(Tabs);

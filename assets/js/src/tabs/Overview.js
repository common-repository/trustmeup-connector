import { hot } from "react-hot-loader/root";
import React, { useState, useEffect } from "react";
import { TextField, Banner, Button, Inline } from "@sparkpost/matchbox";
import Section from "../components/Section";
import { i18n, hasSetApiCredentials } from "../utils/utils";
import { useAppState } from "../utils/context";
import ConnectedProductsTable from "../components/ConnectedProductsTable";
import { CloudDownload, LinkOff, Search } from "@sparkpost/matchbox-icons";
import {
  refreshProducts,
  resyncProducts,
  disconnectProducts,
} from "../utils/ajax";

const Overview = () => {
  const { fields, updateField, isLoading, setLoading, displayNotice } =
    useAppState();
  const [search, setSearch] = useState("");

  /**
   * Refresh on first load.
   */
  useEffect(() => {
    onRefreshClick();
  }, []);

  /**
   * When connected products change, keep a flat list of Woo producst IDs.
   */
  useEffect(() => {
    updateField(
      "connected_woo_products_ids",
      fields.connected_products
        .map((p) => p.synced_with.map((pp) => pp.id).flat())
        .flat()
    );
  }, [fields.connected_products]);

  /**
   * When refreshing the products list.
   */
  const onResyncClick = async (e = null) => {
    if (e) {
      e.preventDefault();
    }

    setLoading(true);
    const result = await resyncProducts();

    if (result.message) {
      displayNotice(
        result.message || result.error,
        result.success ? "success" : "error"
      );
    }

    if (result.fields) {
      if (result.fields.connected_products) {
        updateField("connected_products", result.fields.connected_products);
      }
    }

    setLoading(false);
  };

  /**
   * When refreshing the products list.
   */
  const onRefreshClick = async (e = null) => {
    if (e) {
      e.preventDefault();
    }

    setLoading(true);
    const result = await refreshProducts();

    if (result.message) {
      displayNotice(
        result.message || result.error,
        result.success ? "success" : "error"
      );
    }

    if (result.fields) {
      if (result.fields.connected_products) {
        updateField("connected_products", result.fields.connected_products);
      }
    }

    setLoading(false);
  };

  /**
   * When reseting all products' connections.
   */
  const onDisconnectAllClick = async (e) => {
    e.preventDefault();

    setLoading(true);
    const result = await disconnectProducts();

    if (result.message) {
      displayNotice(
        result.message || result.error,
        result.success ? "success" : "error"
      );
    }

    if (result.fields) {
      if (result.fields.connected_products) {
        updateField("connected_products", result.fields.connected_products);
      }
    }

    setLoading(false);
  };

  //=======================================================
  //
  //  #####    #####  ##     ##  ####    #####  #####
  //  ##  ##   ##     ####   ##  ##  ##  ##     ##  ##
  //  #####    #####  ##  ## ##  ##  ##  #####  #####
  //  ##  ##   ##     ##    ###  ##  ##  ##     ##  ##
  //  ##   ##  #####  ##     ##  ####    #####  ##   ##
  //
  //=======================================================

  if (!hasSetApiCredentials(fields)) {
    return (
      <Section>
        <Banner size="small" status="warning" onDismiss={() => {}}>
          {i18n("Text.CredentialsMissing")}
        </Banner>
      </Section>
    );
  }

  return (
    <>
      <nav className="actions-bar">
        <Inline align="right">
          {fields.connected_products.length > 0 &&
            fields.connected_products.filter((p) => p.synced_with.length > 0)
              .length > 0 && (
              <Button
                size="small"
                variant="text"
                color="red"
                disabled={isLoading}
                className="action-right"
                onClick={(e) =>
                  window.confirm(i18n("Button.DisconnectAll?")) &&
                  onDisconnectAllClick(e)
                }
              >
                <Button.Icon as={LinkOff} size={20} mr="200"></Button.Icon>
                {i18n("Text.DisconnectAllProducts")}
              </Button>
            )}
          <Button
            size="small"
            variant="text"
            disabled={isLoading}
            onClick={onResyncClick}
          >
            <Button.Icon as={CloudDownload} size={20} mr="200"></Button.Icon>
            {i18n("Button.SyncWithTMU")}
          </Button>
        </Inline>
      </nav>
      <Section
        className="connected-products"
        edgy={true}
        title={i18n("Text.ConnectedProducts")}
      >
        <div className="search-section action-right">
          <TextField
            id="search"
            label={null}
            placeholder={i18n("Field.SearchProducts")}
            name="search"
            value={search}
            prefix={<Search size={24} />}
            onChange={(e) => {
              setSearch(e.target.value);
            }}
          />
        </div>
        <ConnectedProductsTable search={search} />
      </Section>
    </>
  );
};

export default hot(Overview);

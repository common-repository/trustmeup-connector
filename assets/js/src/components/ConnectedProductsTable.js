import { hot } from "react-hot-loader/root";
import React, { useState, useEffect } from "react";
import { i18n } from "../utils/utils";
import { Table, Button, useModal, Modal } from "@sparkpost/matchbox";
import { LinkOff, AddCircle, Edit } from "@sparkpost/matchbox-icons";
import ConnectorPopupContent from "./ConnectorPopupContent";
import { useAppState } from "../utils/context";
import { connectProduct, disconnectProduct } from "../utils/ajax";

const ConnectedProductsTable = ({ search }) => {
  const { fields, isLoading, setLoading, displayNotice, updateField } =
    useAppState();
  const { getActivatorProps, getModalProps } = useModal();
  const [productToConnect, setProductToConnect] = useState(null);
  const [selection, updateSelection] = useState([]);

  useEffect(() => {
    updateSelection(
      productToConnect ? productToConnect.synced_with.map((p) => p.id) : []
    );
  }, [productToConnect]);

  /**
   * Select or un-select a product in the popup.
   */
  const selectProduct = (id) => {
    let newSelection;

    // Add to selection.
    if (!selection.includes(id)) {
      newSelection = [...selection, id];
    }

    // Remove from selection.
    else {
      newSelection = selection.filter((iid) => iid !== id);
    }

    updateSelection(() => newSelection);
  };

  /**
   * Select all available products.
   */
  const selectAllAvailableProducts = () => {
    updateSelection(
      window.TrustMeUp.data.fields.woo_products
        .filter(
          (p) =>
            !fields.connected_woo_products_ids.includes(p.id) ||
            selection.includes(p.id)
        )
        .map((p) => p.id)
    );
  };

  /**
   * Save a new connection between TMU product and Woo product(s).
   */
  const connectProducts = async () => {
    getModalProps().onClose();
    setLoading(true);
    const result = await connectProduct(productToConnect.id, selection);

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

    setProductToConnect(null);
    setLoading(false);
  };

  /**
   * When disconnecting a single product.
   */
  const onDisconnectSingleClick = async (product_id) => {
    setLoading(true);
    const result = await disconnectProduct(product_id);

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

  return (
    <div>
      <Table className="connected-products-table">
        <thead>
          <Table.Row header>
            <Table.HeaderCell className="col-name">
              {i18n("Text.TrustMeUpProduct")}
            </Table.HeaderCell>
            <Table.HeaderCell className="col-connections">
              {i18n("Text.WooProduct")}
            </Table.HeaderCell>
          </Table.Row>
        </thead>
        <tbody>
          {fields.connected_products.length === 0 && (
            <Table.Row>
              <Table.Cell colSpan="2">
                <p className="empty-table-message">
                  {i18n("Text.NoConnectedProducts")}
                </p>
              </Table.Cell>
            </Table.Row>
          )}
          {fields.connected_products
            .filter((p) => {
              if (search === "") {
                return true;
              }

              return (
                p.synced_with
                  .map((pp) => pp.name.toLowerCase())
                  .filter((name) => name.includes(search)).length > 0
              );
            })
            .map((product, p) => (
              <Table.Row key={`pendingProductRow${p}`}>
                <Table.Cell className="col-name">
                  <h6>
                    <span className="product-name-text">{product.name}</span>
                  </h6>
                  <ul className="meta">
                    <li>
                      <span className="discount">{product.discount} %</span>
                    </li>
                    {product.synced_with.length > 0 && (
                      <li>
                        <span
                          className="single-disconnect link"
                          onClick={() => {
                            window.confirm(
                              `${i18n("Button.Disconnect")} ${product.name}?`
                            ) && onDisconnectSingleClick(product.id);
                          }}
                        >
                          <LinkOff size="18" className="icon" />{" "}
                          {i18n("Button.Disconnect")}
                        </span>
                      </li>
                    )}
                  </ul>
                </Table.Cell>
                <Table.Cell className="col-connections">
                  {product.synced_with.length > 0 && (
                    <ul className="woo-products">
                      {product.synced_with.map((wooProduct) => {
                        return (
                          <li
                            key={`connectedProduct_${product.id}_${wooProduct.id}`}
                          >
                            <a href={wooProduct.permalink} target="_blank">
                              {/* <img
                              src={wooProduct.thumbnail}
                              alt={wooProduct.name}
                            /> */}
                              <h6>{wooProduct.name}</h6>
                            </a>
                          </li>
                        );
                      })}
                    </ul>
                  )}
                  {product.synced_with.length === 0 ? (
                    <Button
                      size="default"
                      disabled={isLoading}
                      color="blue"
                      className="connect-button-popup-opener connect"
                      variant="mutedOutline"
                      onClick={() => {
                        setProductToConnect(product);
                        getActivatorProps().onClick();
                      }}
                    >
                      <Button.Icon
                        as={AddCircle}
                        size={20}
                        mr={8}
                      ></Button.Icon>
                      <span>{i18n("Button.ConnectProducts")}</span>
                    </Button>
                  ) : (
                    <a
                      href="#"
                      className="link connect-button-popup-opener edit"
                      onClick={(e) => {
                        e.preventDefault();
                        setProductToConnect(product);
                        getActivatorProps().onClick();
                      }}
                    >
                      <Button.Icon as={Edit} size={16} mr={4}></Button.Icon>
                      <span>{i18n("Button.EditConnectedProducts")}</span>
                    </a>
                  )}
                </Table.Cell>
              </Table.Row>
            ))}
        </tbody>
      </Table>
      {productToConnect && (
        <Modal
          {...getModalProps()}
          onClose={() => {
            getModalProps().onClose();
          }}
          id="connector-popup"
          maxWidth="60%"
        >
          <Modal.Header showCloseButton>
            {i18n("Text.ConnectorPopupTitle")} <br />
            <small>
              {productToConnect.name} — {productToConnect.discount} %
            </small>
          </Modal.Header>
          <Modal.Content>
            <ConnectorPopupContent
              selectProduct={selectProduct}
              selectAllAvailableProducts={selectAllAvailableProducts}
              selection={selection}
            />
            <div className="modal-footer">
              <Button
                className="cancel-button"
                variant="text"
                color="red"
                mr={20}
                onClick={() => {
                  getModalProps().onClose();
                  setProductToConnect(null);
                  updateSelection([]);
                }}
              >
                {i18n("Button.Cancel")}
              </Button>
              <Button
                className="connect-button"
                onClick={connectProducts}
                disabled={selection.length === 0}
              >
                {i18n("Button.Save")}
              </Button>
            </div>
          </Modal.Content>
        </Modal>
      )}
    </div>
  );
};

export default hot(ConnectedProductsTable);

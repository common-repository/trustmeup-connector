import { hot } from "react-hot-loader/root";
import React, { useEffect, useState } from "react";
import { Button, TextField } from "@sparkpost/matchbox";
import { i18n } from "../utils/utils";
import { useAppState } from "../utils/context";
import { Search } from "@sparkpost/matchbox-icons";

const ConnectorPopupContent = ({
  selectProduct,
  selectAllAvailableProducts,
  selection,
}) => {
  const { fields } = useAppState();
  const [search, setSearch] = useState("");
  const [products, setProducts] = useState([]);
  const [categorieSearch, setCategorieSearch] = useState("");

  useEffect(() => {
    setProducts(
      window.TrustMeUp.data.fields.woo_products.filter((wooProduct) => {
        if (search === "" && categorieSearch === "") {
          return true;
        } else if(categorieSearch === "" && search !== ""){
          return wooProduct.name.toLowerCase().includes(search.toLowerCase());
        }else if ( search === ""  && categorieSearch !== "" ){
          console.log(wooProduct.categories.map(p => p.toLowerCase()))
          return wooProduct.categories.map(p => p.toLowerCase()).includes(categorieSearch.toLowerCase());
        }
      })
    );
  }, [, search, categorieSearch]);  

  return (
    <div id="search-widget-container">
      <div className="instructions">
        <p>{i18n("Text.ConnectorPopupMultipleDesc")}</p>
        <Button
          size="small"
          mt="400"
          variant="outline"
          onClick={selectAllAvailableProducts}
        >
          {i18n("Button.SelectAllAvailable")}
        </Button>
        <ul className="products-legend">
          <li className="title">{i18n("Text.Legend")}</li>
          <li className="legend-item already-connected">
            {i18n("Text.LegendAlreadyConnected")}
          </li>
          <li className="legend-item connected-with-other">
            {i18n("Text.LegendConnectedWithOther")}
          </li>
          <li className="legend-item available">
            {i18n("Text.LegendAvailable")}
          </li>
        </ul>
      </div>
      <div className="widget">
        <div className="search-section">
        <TextField
            id="categorieSearch"
            label={null}
            placeholder={i18n("Field.CategoriesProducts")}
            name="categorieSearch"
            value={categorieSearch}
            prefix={<Search size={24} />}
            suffix={
              categorieSearch !== "" && (
                <span className="search-stats">
                  {products.length} /{" "}
                  {window.TrustMeUp.data.fields.woo_products.length}
                </span>
              )
            }
            onChange={(e) => {
              setSearch("")
              return setCategorieSearch(e.target.value);
            }}
          />
        </div>
        <div className="search-section">
          <TextField
            id="search"
            label={null}
            placeholder={i18n("Field.SearchProducts")}
            name="search"
            value={search}
            prefix={<Search size={24} />}
            suffix={
              search !== "" && (
                <span className="search-stats">
                  {products.length} /{" "}
                  {window.TrustMeUp.data.fields.woo_products.length}
                </span>
              )
            }
            onChange={(e) => {
              setCategorieSearch("")
              return setSearch(e.target.value);
            }}
          />
        </div>
        {window.TrustMeUp.data.fields.woo_products.length === 0 && (
          <p>{i18n("Text.NoWooProducts")}</p>
        )}
        {search !== "" && products.length === 0 && (
          <p>{i18n("Text.NoWooProductsAfterFilter")}</p>
        )}
        {products.length > 0 && (
          <ul className="woo-products">
            {products.map((wooProduct, index) => {
              const isSelected = selection.includes(wooProduct.id);
              const isSelectedElsewhere =
                !isSelected &&
                fields.connected_woo_products_ids.includes(wooProduct.id);
              let discount = null;

              if (isSelected || isSelectedElsewhere) {
                const connected_tmu_product = fields.connected_products.find(
                  (cp) =>
                    cp.synced_with.map((s) => s.id).includes(wooProduct.id)
                );

                if (connected_tmu_product) {
                  discount = connected_tmu_product.discount;
                }
              }

              return (
                <li
                  key={`wooProduct${wooProduct.id}`}
                  onClick={() => selectProduct(wooProduct.id)}
                  className={`${isSelected ? "selected" : ""} ${
                    isSelectedElsewhere ? "selected-elsewhere" : ""
                  }`}
                >
                  <img src={wooProduct.thumbnail} alt={wooProduct.name} />
                  <h6>{wooProduct.name}</h6>
                  {discount && <span className="discount">{discount}%</span>}
                </li>
              );
            })}
          </ul>
        )}
      </div>
    </div>
  );
};

export default hot(ConnectorPopupContent);

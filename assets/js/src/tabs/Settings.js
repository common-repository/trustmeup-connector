import { hot } from "react-hot-loader/root";
import React from "react";
import {
  Button,
  TextField,
  Columns,
  Column,
  Checkbox,
  Banner,
} from "@sparkpost/matchbox";
import Section from "../components/Section";
import Debug from "../components/Debug";
import { i18n } from "../utils/utils";
import { saveFields } from "../utils/ajax";
import { useAppState } from "../utils/context";

const Settings = () => {
  const { fields, updateField, setLoading, displayNotice } = useAppState();

  const onApiCredentialsSaveClick = async (e) => {
    e.preventDefault();
    setLoading(true);

    let result = null;

    if (fields.api_environment === "prod") {
      result = await saveFields({
        api_environment: "prod",
        api_client_id: fields.api_client_id,
        api_password: fields.api_password,
      });
    } else if (fields.api_environment === "beta") {
      result = await saveFields({
        api_environment: "beta",
        api_client_id_beta: fields.api_client_id_beta,
        api_password_beta: fields.api_password_beta,
      });
    }

    displayNotice(
      result.message || result.error,
      result.success ? "success" : "error"
    );

    if (result.fields && typeof result.fields.merchant_token !== "undefined") {
      updateField("merchant_token", result.fields.merchant_token);
    }

    setLoading(false);
  };

  return (
    <>
      <Section title={i18n("Text.ApiCredentials")}>
        <Checkbox
          id="api_environment"
          name="api_environment"
          checked={fields.api_environment === "beta"}
          label={i18n("Field.EnvironmentCheckbox")}
          onChange={(e) => {
            const value = e.target.checked ? "beta" : "prod";
            updateField("api_environment", value);
            updateField("merchant_token", null);
          }}
        />
        {fields.api_environment === "prod" ? (
          <p
            className="section-intro"
            dangerouslySetInnerHTML={{
              __html: i18n("Text.TextBeforeAPICreds"),
            }}
          />
        ) : (
          <p
            className="section-intro"
            dangerouslySetInnerHTML={{
              __html: i18n("Text.TextBeforeAPICredsBeta"),
            }}
          />
        )}

        <Columns>
          {fields.api_environment === "prod" ? (
            <>
              <Column width={2 / 5}>
                <TextField
                  id="api_client_id"
                  label={i18n("Field.ClientID")}
                  placeholder={i18n("Field.ClientID")}
                  name="api_client_id"
                  value={fields.api_client_id}
                  onChange={(e) => {
                    updateField(e.target.name, e.target.value);
                  }}
                />
              </Column>
              <Column width={2 / 5}>
                <TextField
                  id="api_password"
                  label={i18n("Field.Password")}
                  placeholder={i18n("Field.Password")}
                  name="api_password"
                  value={fields.api_password}
                  onChange={(e) => {
                    updateField(e.target.name, e.target.value);
                  }}
                />
              </Column>
            </>
          ) : (
            <>
              <Column width={2 / 5}>
                <TextField
                  id="api_client_id_beta"
                  label={`${i18n("Field.ClientID")} (beta)`}
                  placeholder={`${i18n("Field.ClientID")} (beta)`}
                  name="api_client_id_beta"
                  value={fields.api_client_id_beta}
                  onChange={(e) => {
                    updateField(e.target.name, e.target.value);
                  }}
                />
              </Column>
              <Column width={2 / 5}>
                <TextField
                  id="api_password_beta"
                  label={`${i18n("Field.Password")} (beta)`}
                  placeholder={`${i18n("Field.Password")} (beta)`}
                  name="api_password_beta"
                  value={fields.api_password_beta}
                  onChange={(e) => {
                    updateField(e.target.name, e.target.value);
                  }}
                />
              </Column>
            </>
          )}
          <Column className="align-bottom">
            <Button
              size="default"
              width="100%"
              variant="outline"
              color="blue"
              disabled={
                (fields.api_environment === "prod" &&
                  (fields.api_client_id === "" ||
                    fields.api_password === "")) ||
                (fields.api_environment === "beta" &&
                  (fields.api_client_id_beta === "" ||
                    fields.api_password_beta === ""))
              }
              onClick={onApiCredentialsSaveClick}
              mutedOutline
            >
              {i18n("Button.Save")}
            </Button>
          </Column>
        </Columns>
        {fields.merchant_token ? (
          <Banner mt="300" size="small" status="info" onDismiss={() => {}}>
            {i18n("Settings.ValidMerchantTokenIs")}{" "}
            <code>{fields.merchant_token}</code>
          </Banner>
        ) : (
          <Banner mt="300" size="small" status="warning" onDismiss={() => {}}>
            {i18n("Settings.InvalidMerchantToken")}
          </Banner>
        )}
      </Section>
    </>
  );
};

export default hot(Settings);

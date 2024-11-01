import { hot } from "react-hot-loader/root";
import React from "react";
import { Panel } from "@sparkpost/matchbox";

const Section = ({
  title,
  subtitle,
  transparent = false,
  edgy = false,
  children,
  className = false,
}) => {
  return (
    <Panel
      className={`section${className ? ` ${className}` : ""}${
        transparent ? " transparent" : ""
      }${edgy ? " edgy" : ""}`}
    >
      {title && <Panel.Header>{title}</Panel.Header>}
      {subtitle && <Panel.SubHeader>{subtitle}</Panel.SubHeader>}
      <Panel.Section className="section-children">{children}</Panel.Section>
    </Panel>
  );
};

export default hot(Section);

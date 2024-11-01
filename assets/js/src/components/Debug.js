import { hot } from "react-hot-loader/root";
import React, { useEffect } from "react";
import { CodeBlock, Snackbar } from "@sparkpost/matchbox";

const Debug = (props) => {
  return <CodeBlock dark code={JSON.stringify(props, undefined, 2)} />;
};

export default hot(Debug);

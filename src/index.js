import { render } from "@wordpress/element";
import React from "react";
import { __ } from "@wordpress/i18n";

import App from "./App";
// Plugin stylesheet.
import "./index.scss";
import 'react-toastify/dist/ReactToastify.css';

// Render the App component into the DOM
render(<App />, document.querySelector("#wpstorm-notify-admin-page"));

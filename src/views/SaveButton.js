/**
 * Import remote dependencies.
 */
import React from "react";
// import "../styles/save-button.scss";

import { __ } from "@wordpress/i18n";

/**
 * This component power the settings component.
 *
 * @since 1.0.0
 */

const SaveButton = (props) => {
  /*const handleClick = () => {
    const button = document.getElementsByClassName("wpstorm-notify-save-button")[0];
    button.classList.add("loading");

    setTimeout(function () {
      button.classList.remove("loading");
      button.classList.add("success");

      setTimeout(function () {
        button.classList.remove("success");
      }, 2000);
    }, 3000);
  };*/

  const { isSaving, buttonText } = props;
  return (
    <button type="submit" className="btn btn-primary mt-3" disabled={isSaving}>
      {buttonText ? buttonText : __("Save Settings", "wpstorm-notify")}
    </button>
    /*<button
      type="submit"
      className="wpstorm-notify-save-button"
      disabled={isSaving}
      onClick={handleClick}
    >
      <span>{buttonText ? buttonText : __("Save Settings", "wpstorm-notify")}</span>
    </button>*/
  );
};

export default SaveButton;

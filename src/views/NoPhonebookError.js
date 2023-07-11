/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";

/**
 * This component power the settings component.
 *
 * @since 1.0.0
 */

const NoPhonebookError = (props) => {
  const { pluginName, ...inputProps } = props;

  return (
    <div className="container">
      <div className="container card bg-warning mb-3">
        <div className="card-body">
          <h5 className="card-title">{__("Warning:", "wpstorm-notify")}</h5>
          <p className="card-text">
            {__(
              "You have not chosen a phonebook for " +
                pluginName +
                " yet. Please go to phonebooks tab and select a phonebook for " +
                pluginName,
              "wpstorm-notify"
            )}
          </p>
        </div>
      </div>
    </div>
  );
};

export default NoPhonebookError;

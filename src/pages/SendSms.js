/**
 * Import remote dependencies.
 */
import React, { useState, useEffect, useContext } from "react";
import { useImmerReducer } from "use-immer";
import { __ } from "@wordpress/i18n";

/**
 * Import local dependencies
 */
import DispatchContext from "../DispatchContext";
import FormInputError from "../views/FormInputError";
import SectionHeader from "../views/SectionHeader";
import LoadingSpinner from "../views/LoadingSpinner";
import AxiosWp from "../function/AxiosWp";
import SettingsForm from "../views/SettingsForm";

function SendSms(props) {
  const appDispatch = useContext(DispatchContext);
  // Init States
  const originalState = {
    inputs: {
      message: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "messageChange",
        rules: "messageRules",
        name: "message",
        type: "textarea",
        label: __("Message:", "wpstorm-notify"),
        required: true,
        infoTitle: __("Usable variables:", "wpstorm-notify"),
        infoBody: __(
          "You can use %name% on message content if you want to send sms to subscribers.",
          "wpstorm-notify"
        ),
      },
      senderNumber: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "senderNumberChange",
        rules: "senderNumberRules",
        name: "senderNumber",
        type: "select",
        label: __("Select sender number:", "wpstorm-notify"),
        options: [
          { value: "1", label: __("Servicing number", "wpstorm-notify") },
          { value: "2", label: __("Advertising number", "wpstorm-notify") },
        ],
        noOptionsMessage: __("No options is available", "wpstorm-notify"),
        required: true,
      },
      toSubscribers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "toSubscribersChange",
        rules: "toSubscribersRules",
        name: "toSubscribers",
        type: "checkbox",
        label: __("Send to newsletter subscribers:", "wpstorm-notify"),
      },
      manuallyNumbers: {
        value: "",
        hasErrors: false,
        errorMessage: "",
        onChange: "manuallyNumbersChange",
        rules: "manuallyNumbersRules",
        name: "manuallyNumbers",
        type: "textarea",
        label: __("Enter numbers manually:", "wpstorm-notify"),
        tooltip: __(
          "Separate numbers with English commas without any spaces.",
          "wpstorm-notify"
        ),
      },
      phonebooks: {
        value: [],
        hasErrors: false,
        errorMessage: "",
        onChange: "phonebooksChange",
        rules: "phonebooksRules",
        name: "phonebooks",
        type: "select_phonebook",
        label: __("Select phonebooks:", "wpstorm-notify"),
        options: [],
        noOptionsMessage: __("No options is available", "wpstorm-notify"),
        isMulti: "isMulti",
      },
    },
    noPhonebooks: true,
    isFetching: false,
    isSaving: false,
    sendCount: 0,
    sectionName: __("Send Sms", "wpstorm-notify"),
  };

  function ourReduser(draft, action) {
    switch (action.type) {
      case "cantFetching":
        draft.isFetching = false;
        return;
      case "all_phonebookOptions":
        draft.noPhonebooks = false;
        draft.inputs.phonebooks.options = action.value;
        return;
      case "noPhonebooks":
        draft.noPhonebooks = true;
        return;
      case "messageChange":
        draft.inputs.message.hasErrors = false;
        draft.inputs.message.value = action.value;
        return;
      case "messageRules":
        if (!action.value.trim()) {
          draft.inputs.message.hasErrors = true;
          draft.inputs.message.errorMessage = __(
            "You must provide a message.",
            "wpstorm-notify"
          );
        }
        return;
      case "senderNumberChange":
        draft.inputs.senderNumber.hasErrors = false;
        draft.inputs.senderNumber.value = action.value;
        return;
      case "toSubscribersChange":
        draft.inputs.toSubscribers.hasErrors = false;
        draft.inputs.toSubscribers.value = action.value;
        return;
      case "manuallyNumbersChange":
        draft.inputs.manuallyNumbers.hasErrors = false;
        draft.inputs.manuallyNumbers.value = action.value;
        return;
      case "phonebooksChange":
        draft.inputs.phonebooks.hasErrors = false;
        draft.inputs.phonebooks.value = action.value;
        return;
      case "submitOptions":
        if (
          !draft.inputs.message.hasErrors &&
          !draft.inputs.phonebooks.hasErrors &&
          !draft.inputs.toSubscribers.hasErrors &&
          !draft.inputs.manuallyNumbers.hasErrors &&
          !draft.inputs.senderNumber.hasErrors
        ) {
          draft.sendCount++;
        }
        return;
      case "saveRequestStarted":
        draft.isSaving = true;
        return;
      case "saveRequestFinished":
        draft.isSaving = false;
        return;
    }
  }

  const [state, dispatch] = useImmerReducer(ourReduser, originalState);

  function handleSubmit(e) {
    e.preventDefault();

    async function sendSms() {
      if (
        !state.inputs.manuallyNumbers.value &&
        !state.inputs.phonebooks.value &&
        !state.inputs.toSubscribers.value
      ) {
        appDispatch({
          type: "flashMessage",
          value: {
            type: "error",
            message: __(
              "Please select at least one phonebook or enter manual number or chose send to newsletter subscribers.",
              "wpstorm-notify"
            ),
          },
        });
      } else {
        try {
          const res = await AxiosWp.post("/wpstorm-notify/v1/send_sms", {
            message: state.inputs.message.value,
            phonebooks: state.inputs.phonebooks.value,
            send_to_subscribers: state.inputs.toSubscribers.value,
            send_fromnum_choice: state.inputs.senderNumber.value.value,
            phones: state.inputs.manuallyNumbers.value,
          });
          if (res.data === "noSubscribers") {
            appDispatch({
              type: "flashMessage",
              value: {
                type: "error",
                message: __(
                  "Sorry. No one is subscriber of newsletter yet.",
                  "wpstorm-notify"
                ),
              },
            });
          } else {
            appDispatch({
              type: "flashMessage",
              value: {
                message: __(
                  "Congrats. Message was sent successfully.",
                  "wpstorm-notify"
                ),
              },
            });
          }
          console.log(res);
        } catch (e) {
          console.log(e);
        }
      }
    }

    sendSms();
  }

  /**
   * Get phonebooks.
   *
   * @since 1.0.0
   */
  function handleNoPhonebooks() {
    dispatch({ type: "noPhonebooks" });
  }

  function handleAllPhonebooks(phonebooksArrayObject) {
    dispatch({
      type: "all_phonebookOptions",
      value: phonebooksArrayObject,
    });
  }

  if (state.isFetching) return <LoadingSpinner />;

  /**
   * The settings form created by mapping over originalState as the main state.
   * For every value on inputs rendered a SettingsFormInput.
   *
   * @since 1.0.0
   */
  return (
    <div>
      <SectionHeader sectionName={state.sectionName} />
      <div>
        <SettingsForm
          dispatchAllPhonebooks={handleAllPhonebooks}
          dispatchNoPhonebooks={handleNoPhonebooks}
          sectionName={state.sectionName}
          inputs={state.inputs}
          handleSubmit={handleSubmit}
          dispatch={dispatch}
          isSaving={state.isSaving}
          buttonText={__("Send Sms", "wpstorm-notify")}
        />
      </div>
    </div>
  );
}

export default SendSms;

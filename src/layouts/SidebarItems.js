/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";

import {
    TbNews,
    TbFilePhone,
    TbNetwork,
    TbSettings2,
    TbAnalyze,
    TbMessage2,
    TbShoppingCart,
    TbCloudDownload,
    TbUserPlus,
    TbUsersGroup,
    TbForms,
    TbMessage2Share,
    TbCreditCard,
    TbAffiliate,
    TbHeadphones
} from "react-icons/tb";

/**
 * Import local dependencies
 */
import Settings from "../pages/Settings";
import LoginNotify from "../pages/LoginNotify";
import Phonebook from "../pages/Phonebook";
import Synchronization from "../pages/Synchronization";
import Comments from "../pages/Comments";
import Woocommerce from "../pages/Woocommerce";
import WooOrderActions from "../pages/WooOrderActions";
import Edd from "../pages/Edd";
import Newsletter from "../pages/Newsletter";
import Aff from "../pages/Aff";
import Membership from "../pages/Membership";
import Integrations from "../pages/Integrations";
import GravityForms from "../pages/GravityForms";
import SendSms from "../pages/SendSms";
import Support from "../pages/Support";

const SidebarItems = [
    {
        path: "/",
        element: Settings,
        name: __("Settings", "wpstorm-notify"),
        icon: <TbSettings2 />,
    },
    {
        path: "/login_notify",
        element: LoginNotify,
        name: __("Login Notify", "wpstorm-notify"),
        icon: <TbUserPlus />,
    },
    {
        path: "/phonebook",
        element: Phonebook,
        name: __("Phonebook", "wpstorm-notify"),
        icon: <TbFilePhone />,
    },
    {
        path: "/gravity_forms",
        element: GravityForms,
        name: __("Gravity Forms", "wpstorm-notify"),
        icon: <TbForms />,
    },
    {
        path: "/synchronization",
        element: Synchronization,
        name: __("Synchronization", "wpstorm-notify"),
        icon: <TbAnalyze />,
    },
    {
        path: "/comments",
        element: Comments,
        name: __("Comments", "wpstorm-notify"),
        icon: <TbMessage2 />,
    },
    {
        path: "/woocommerce",
        element: Woocommerce,
        name: __("WooCommerce", "wpstorm-notify"),
        icon: <TbShoppingCart />,
    },
    {
        path: "/woocommerce_order_actions",
        element: WooOrderActions,
        name: __("WooCommerce order actions", "wpstorm-notify"),
        icon: <TbCreditCard />,
    },
    {
        path: "/edd",
        element: Edd,
        name: __("Edd", "wpstorm-notify"),
        icon: <TbCloudDownload />,
    },
    {
        path: "/newsletter",
        element: Newsletter,
        name: __("Newsletter", "wpstorm-notify"),
        icon: <TbNews />,
    },
    {
        path: "/aff",
        element: Aff,
        name: __("Affiliate", "wpstorm-notify"),
        icon: <TbAffiliate />,
    },
    {
        path: "/membership",
        element: Membership,
        name: __("Membership", "wpstorm-notify"),
        icon: <TbUsersGroup />,
    },
    {
        path: "/send_sms",
        element: SendSms,
        name: __("Send Sms", "wpstorm-notify"),
        icon: <TbMessage2Share />,
    },
    {
        path: "/support",
        element: Support,
        name: __("Support", "wpstorm-notify"),
        icon: <TbHeadphones />,
    },
    {
        path: "/integrations",
        element: Integrations,
        name: __("Integrations", "wpstorm-notify"),
        icon: <TbNetwork />,
    },
];

export default SidebarItems;

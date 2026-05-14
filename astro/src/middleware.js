import { defineMiddleware } from "astro:middleware";
import { redirectToDefaultLocale, middleware } from "astro:i18n";

export const onRequest = defineMiddleware(async (ctx, next) => {
    if (ctx.url.pathname.startsWith("/about")) {
        return next();
    } else {
        return next();
    }
});

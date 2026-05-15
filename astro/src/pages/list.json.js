const pages = Object.values(
    import.meta.glob('./**/*.mdx', { eager: true })
);

export async function GET() {
    const payload = {};
    for (const page of pages) {
        let id = page.frontmatter.form_id || page.url.split('/')[1].replace(/-\d+\.\d+\.\d+/, '');
        if (!Object.hasOwn(payload, id)) {
            payload[id] = {};
        }
        let language = page.frontmatter.form_id ? 'index' : page.url.split('/')[2];
        payload[id][language] = page;
    }

    return new Response(
        JSON.stringify(payload),
        { headers: { 'Content-Type': 'application/json' } }
    );
}

const pages = Object.values(
    import.meta.glob('./*.mdx', { eager: true })
);

export async function GET() {
    return new Response(
        JSON.stringify(pages),
        { headers: { 'Content-Type': 'application/json' } }
    );
}

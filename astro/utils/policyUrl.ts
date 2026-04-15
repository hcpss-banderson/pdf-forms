export default function policyUrl(policy: string) {
    let parent = policy.slice(0, 1) + '000';
    return `https://policy.hcpss.org/${parent}/${policy}/`;
}

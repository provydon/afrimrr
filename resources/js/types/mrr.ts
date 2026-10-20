export interface Founder {
    name: string | null;
    username: string | null;
    avatar_url?: string | null;
    headline?: string | null;
    twitter?: string | null;
    twitter_url?: string | null;
    website?: string | null;
    bio?: string | null;
}

export interface FounderStats {
    total_mrr_display: string;
    arr_display: string;
    total_startups: number;
    verified_startups: number;
    followers: number | null;
    followers_display: string | null;
}

export interface StartupCardData {
    rank?: number;
    name: string;
    slug: string | null;
    is_anonymous?: boolean;
    tagline: string | null;
    logo_url: string | null;
    category: string;
    category_label: string;
    country: string;
    country_label: string;
    is_verified: boolean;
    verification_source: string | null;
    mrr_usd: number;
    mrr_usd_display: string;
    mrr_usd_compact: string;
    mrr_local_display: string;
    display_currency: string;
    mom_growth: number | null;
    is_for_sale: boolean;
    asking_price_display: string | null;
    revenue_multiple: number | null;
    founder: Founder;
}

export interface StartupProfileData extends StartupCardData {
    description: string | null;
    website: string | null;
    founded_year: number | null;
    active_subscriptions: number;
    last_synced_at: string | null;
    arr_usd_display: string;
    arr_local_display: string;
    history: { date: string; mrr_usd: number }[];
}

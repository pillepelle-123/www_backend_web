export type Offer = {
  id: number;
  title: string;
  description: string;
  offerer_type: string;
  offer_user: string;
  offer_company: string;
  logo_path: string;
  reward_total_cents: number;
  reward_offerer_percent: number;
  created_at: string;
  average_rating: number;
  status: "active" | "inactive" | "closed" | "matched";
  industry?: string;
};
export type Offer = {
  id: number;
  offer_title: string;
  "user.name": string;
  reward_total_cents: number;
  reward_offerer_percent: number;
  status: string;
  user: { name: string };
  //   status: 'active' | 'inactive';
//   user: { "user.name": string };
//   company: { name: string };
//   created_at: string;
};

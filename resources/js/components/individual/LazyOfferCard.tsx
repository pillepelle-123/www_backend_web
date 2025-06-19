import { useInView } from 'react-intersection-observer';
import { OfferCard } from './OfferCard';
import { Offer } from '@/pages/offers/index';

interface LazyOfferCardProps {
  offer: Offer;
}

export function LazyOfferCard({ offer }: LazyOfferCardProps) {
  const { ref, inView } = useInView({
    triggerOnce: true,
    threshold: 0,
    rootMargin: '0px 0px',
    initialInView: false,
  });

  return (
    <div
      ref={ref}
      className={`transition-all duration-700 ease-in-out transform ${
        inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'
      }`}
    >
      <OfferCard offer={offer} />
    </div>
  );
}

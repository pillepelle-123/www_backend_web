import { useState, useEffect } from 'react';
import axios from 'axios';
import { Offer } from '@/types/offer';

export const useOffers = (page: number = 1) => {
  const [offers, setOffers] = useState<Offer[]>([]);
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
  });

  useEffect(() => {
    const fetchOffers = async () => {
      const { data } = await axios.get(`/api/v1/offers?page=${page}`);
      setOffers(data.data);
      setPagination({
        current_page: data.current_page,
        last_page: data.last_page,
      });
    };

    fetchOffers();
  }, [page]);

  return { offers, pagination };
};

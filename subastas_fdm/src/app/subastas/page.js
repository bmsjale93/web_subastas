import React from "react";
import AuctionCard from "../../components/AuctionCard";
import { auctions } from "../../constants/auctions";
import Image from "next/image";
import logo from "../../../public/logo-fdm.png";

export default function Auctions() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-800 to-black py-10">
      <div className="container mx-auto p-4 bg-white rounded-lg shadow-lg">
        <div className="text-center mb-8">
          <Image
            src={logo}
            alt="Logo"
            className="mx-auto mb-4 w-48 h-48 sm:w-40 sm:h-40 md:w-40 md:h-40"
          />
        </div>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {auctions.map((auction) => (
            <AuctionCard
              key={auction.id}
              id={auction.id}
              title={auction.title}
              value={auction.value}
              endDate={auction.endDate}
              process={auction.process}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
